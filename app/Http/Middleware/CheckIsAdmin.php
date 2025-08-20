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
        // Se o usuário NÃO estiver logado OU o cargo dele NÃO for 'admin'
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            // Aborta a requisição e mostra uma página de "Acesso Proibido".
            abort(403, 'ACESSO NÃO AUTORIZADO');
        }

        return $next($request);
    }
}
