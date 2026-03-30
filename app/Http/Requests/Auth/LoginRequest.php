<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
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

        $username = $this->email; // Pode ser "daniel.castro", o CPF, ou o email inteiro
        $password = $this->password;

        // === 1. TENTATIVA DE AUTENTICAÇÃO VIA ACTIVE DIRECTORY ===
        $ldap_host = env('LDAP_HOST');
        $ldap_base_dn = env('LDAP_BASE_DN');
        $ldap_bind_user = env('LDAP_USERNAME');
        $ldap_bind_pass = env('LDAP_PASSWORD');

        if ($ldap_host && $ldap_bind_user) {
            try {
                $ldap_conn = @ldap_connect($ldap_host);
                if ($ldap_conn) {
                    ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
                    ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

                    
                    // Desiste se a rede não conectar em 3 segundos
                    ldap_set_option($ldap_conn, LDAP_OPT_NETWORK_TIMEOUT, 3);
                    // Desiste se o AD demorar mais de 3 segundos para responder à pesquisa
                    ldap_set_option($ldap_conn, LDAP_OPT_TIMELIMIT, 3);
                    // -------------------------------
                    // Conecta no AD com a conta de serviço do pfSense
                    if (@ldap_bind($ldap_conn, $ldap_bind_user, $ldap_bind_pass)) {

                        //  Extrai o domínio a partir do Base DN (Ex: DC=srv-debian,DC=ifnmg-almenara -> srv-debian.ifnmg-almenara)
                        $ad_domain = str_ireplace(['DC=', ','], ['', '.'], $ldap_base_dn);

                        // Filtro de busca "Teia de Aranha": Procura pelo CPF (sAMAccountName), pelo UPN ou forçando a junção do nome com o domínio
                        $filter = "(|(sAMAccountName={$username})(mail={$username})(userPrincipalName={$username})(userPrincipalName={$username}@{$ad_domain}))";

                        $search = @ldap_search($ldap_conn, $ldap_base_dn, $filter);
                        $entries = @ldap_get_entries($ldap_conn, $search);

                        if ($entries['count'] > 0) {
                            $user_dn = $entries[0]['dn'];
                            $user_upn = $entries[0]['userprincipalname'][0] ?? null;
                            $user_mail = $entries[0]['mail'][0] ?? null;
                            $user_sam = $entries[0]['samaccountname'][0] ?? null;

                            // O AD prefere autenticar usando o UPN (ex: daniel.castro@srv-debian...). Se não tiver, usa o DN.
                            $login_attribute = $user_upn ? $user_upn : $user_dn;

                            // Tenta logar usando a senha que a pessoa digitou na tela
                            if (@ldap_bind($ldap_conn, $login_attribute, $password)) {

                                // O AD APROVOU A SENHA! Vamos achar o perfil importado no banco de dados do SIGA-IF
                                $user = \App\Models\User::where('email', $username)
                                    ->orWhere('email', $user_mail)
                                    ->orWhere('email', $user_sam)
                                    ->orWhere('email', $user_upn)
                                    // Considera também a formatação padrão do IF caso a importação tenha salvo assim
                                    ->orWhere('email', "{$username}@ifnmg.edu.br")
                                    ->first();

                                if ($user) {
                                    // Aprovado e importado! Libera a entrada.
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
                // Se o AD cair ou a rede falhar, ele segue silenciosamente para tentar o login local no banco
            }
        }

        // === 2. FALLBACK: AUTENTICAÇÃO LOCAL (Conta Admin Original) ===
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey(), 300);

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
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
