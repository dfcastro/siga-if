<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Define a lista de perfis que podem acessar as rotas de gerenciamento
        $allowedRoles = ['admin', 'porteiro', 'fiscal'];

        // Verifica se o perfil do usuário logado está na lista de perfis permitidos
        if (!in_array(auth()->user()->role, $allowedRoles)) {
            abort(403, 'Acesso não autorizado!');
        }

        return $next($request);
    }
}