<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleRoleMiddleware
{
    /**
     * Lida com uma requisição de entrada.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  // Aceita múltiplos perfis (ex: 'admin', 'fiscal')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Verifica se o utilizador está logado e se o seu perfil está na lista de perfis permitidos.
        if (! $request->user() || ! in_array($request->user()->role, $roles)) {
            // Se não tiver permissão, retorna um erro "403 - Acesso não autorizado".
            abort(403, 'ACESSO NÃO AUTORIZADO.');
        }

        return $next($request);
    }
}