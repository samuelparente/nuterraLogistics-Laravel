<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Throwable;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\RedirectResponse;

class Handler extends ExceptionHandler
{
    protected $levels = [];

    protected $dontReport = [];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function render($request, Throwable $e): Response|RedirectResponse
    {
        if ($e instanceof TokenMismatchException) {
            return redirect()
                ->route('login')
                ->with('message', 'A sua sessão expirou. Por favor, faça login novamente.');
        }

        return parent::render($request, $e);
    }
}
