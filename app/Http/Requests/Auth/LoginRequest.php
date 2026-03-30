<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // <-- Adicionado para verificar a senha local
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
            'g-recaptcha-response' => ['required', 'captcha'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $username = $this->email;
        $password = $this->password;

        // === 1. TENTATIVA DE AUTENTICAÇÃO VIA ACTIVE DIRECTORY ===
        $ldap_host = config('services.ldap.host', '');
        $ldap_base_dn = config('services.ldap.base_dn', '');
        $ldap_bind_user = config('services.ldap.username', '');
        $ldap_bind_pass = config('services.ldap.password', '');

        if ($ldap_host && $ldap_bind_user && function_exists('ldap_connect')) {
            try {
                $ldap_conn = @ldap_connect($ldap_host);
                if ($ldap_conn) {
                    ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
                    ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);
                    ldap_set_option($ldap_conn, LDAP_OPT_NETWORK_TIMEOUT, 3);
                    ldap_set_option($ldap_conn, LDAP_OPT_TIMELIMIT, 3);

                    if (@ldap_bind($ldap_conn, $ldap_bind_user, $ldap_bind_pass)) {

                        $ad_domain = str_ireplace(['DC=', ','], ['', '.'], (string) $ldap_base_dn);
                        $filter = "(|(sAMAccountName={$username})(mail={$username})(userPrincipalName={$username})(userPrincipalName={$username}@{$ad_domain}))";

                        $search = @ldap_search($ldap_conn, $ldap_base_dn, $filter);
                        $entries = @ldap_get_entries($ldap_conn, $search);

                        if ($entries['count'] > 0) {
                            $user_dn = $entries[0]['dn'];
                            $user_upn = $entries[0]['userprincipalname'][0] ?? null;
                            $user_mail = $entries[0]['mail'][0] ?? null;
                            $user_sam = $entries[0]['samaccountname'][0] ?? null;

                            $login_attribute = $user_upn ? $user_upn : $user_dn;

                            if (@ldap_bind($ldap_conn, $login_attribute, $password)) {

                                $user = \App\Models\User::where('email', $username)
                                    ->orWhere('email', $user_mail)
                                    ->orWhere('email', $user_sam)
                                    ->orWhere('email', $user_upn)
                                    ->orWhere('email', "{$username}@ifnmg.edu.br")
                                    ->first();

                                if ($user) {
                                    Auth::login($user, $this->boolean('remember'));
                                    RateLimiter::clear($this->throttleKey());
                                    return;
                                } else {
                                    throw ValidationException::withMessages([
                                        'email' => 'Sua senha da rede está correta, mas seu usuário ainda não tem permissão de acesso ao SIGA-IF.',
                                    ]);
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                // Se falhar no AD, segue silenciosamente para a validação local
            }
        }

        // === 2. FALLBACK: AUTENTICAÇÃO LOCAL INTELIGENTE ===
        // Em vez do Auth::attempt estrito, buscamos o usuário de forma flexível
        $localUser = \App\Models\User::where('email', $username)
            ->orWhere('name', $username) // Permite logar digitando o Nome exato
            ->orWhere('email', 'like', "{$username}@%") // Permite logar só com o prefixo (antes do @)
            ->first();

        // Se encontrou o usuário e a senha local for compatível
        if ($localUser && Hash::check($password, $localUser->password)) {
            Auth::login($localUser, $this->boolean('remember'));
            RateLimiter::clear($this->throttleKey());
            return;
        }

        // Se chegou até aqui, as credenciais falharam tanto no AD quanto no Banco Local
        RateLimiter::hit($this->throttleKey(), 300);

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 3)) {
            return;
        }

        event(new Lockout($this));
        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')) . '|' . $this->ip());
    }
}
