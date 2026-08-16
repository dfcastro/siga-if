<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Str;

class ImportAdUser extends Command
{
    protected $signature = 'ad:import {username?}';
    protected $description = 'Importa um usuário do Active Directory e cria no SIGA-IF';

    public function handle()
    {
        $username = $this->argument('username');
        
        if (!$username) {
            $username = $this->ask('Qual o login do AD (sAMAccountName ou email)?');
        }

        $this->info("🔍 Buscando {$username} no servidor AD...");

        $ldap_host = env('LDAP_HOST');
        $ldap_base_dn = env('LDAP_BASE_DN');
        $ldap_bind_user = env('LDAP_USERNAME');
        $ldap_bind_pass = env('LDAP_PASSWORD');

        $ldap_conn = @ldap_connect($ldap_host);
        if (!$ldap_conn) {
            $this->error("❌ Falha crítica: Não foi possível alcançar o servidor AD.");
            return;
        }

        ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

        if (!@ldap_bind($ldap_conn, $ldap_bind_user, $ldap_bind_pass)) {
            $this->error("❌ Falha de Autenticação: Verifique a senha da conta de serviço do pfSense.");
            return;
        }

        // Procura pelo nome de login ou email do servidor
        $filter = "(|(sAMAccountName={$username})(mail={$username}))";
        $search = @ldap_search($ldap_conn, $ldap_base_dn, $filter);
        $entries = @ldap_get_entries($ldap_conn, $search);

        if ($entries['count'] === 0) {
            $this->error("❌ Usuário '{$username}' não encontrado na base do AD.");
            return;
        }

        // Extrai os dados essenciais (Nome e Email)
        $name = $entries[0]['cn'][0] ?? $entries[0]['displayname'][0] ?? 'Sem Nome Registrado';
        $email = $entries[0]['mail'][0] ?? $entries[0]['userprincipalname'][0] ?? "{$username}@ifnmg.edu.br";

        $this->info("✅ Encontrado: {$name} ({$email})");

        // Verifica se já foi importado
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            $this->warn("⚠️ Este usuário já está importado no SIGA-IF com o perfil: " . strtoupper($existingUser->role));
            return;
        }

        // Pergunta qual será o perfil de forma interativa
        $role = $this->choice(
            'Qual perfil este usuário terá no sistema?',
            ['porteiro', 'fiscal', 'admin'],
            0 // Porteiro como padrão
        );

        $fiscalType = null;
        if ($role === 'fiscal') {
            $fiscalType = $this->choice(
                'Qual tipo de frota este fiscal vai gerenciar?',
                ['official' => 'Oficial', 'private' => 'Particular', 'both' => 'Ambas'],
                0
            );
            $fiscalType = array_keys(['official' => 'Oficial', 'private' => 'Particular', 'both' => 'Ambas'])[$fiscalType];
        }

        // Cria o usuário com senha "fictícia" aleatória, pois ele usará a do AD
        User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt(Str::random(32)), 
            'role' => $role,
            'fiscal_type' => $fiscalType,
        ]);

        $this->info("🎉 Sucesso! Usuário {$name} autorizado no sistema como " . strtoupper($role) . ".");
        $this->line("Ele já pode fazer login na tela inicial usando a mesma senha do computador da sala dele.");
    }
}