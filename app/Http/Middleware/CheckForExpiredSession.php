<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckForExpiredSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $isLoginRoute = $request->route()?->getName() === 'login'
            || $request->is('*/login');
        
        // Verifica se a requisição é para a rota de login
        if ($isLoginRoute && $request->hasSession()) {
            
            // Verifica se a sessão contém uma mensagem flash
            $message = null;
            if ($request->session()->has('message')) {
                // Se sim, armazena o valor numa variável temporária
                $message = $request->session()->get('message');
            }

            // Invalida a sessão antiga e gera uma nova
            // (Esta é a parte que causa o problema e agora está controlada)
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Se existia uma mensagem, colocamo-la de volta na nova sessão
            if ($message) {
                $request->session()->flash('message', $message);
            }
        }

        return $next($request);
    }
}