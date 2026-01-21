<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
     ->withMiddleware(function (Middleware $middleware) {

         $middleware->web(append: [
            \App\Http\Middleware\CheckForExpiredSession::class,
        ]);
        
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            return redirect()
                ->route('login')
                ->with('message', 'A sua sessão expirou. Por favor, faça login novamente.');
        });
    })
    
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('reports:validades')
            ->dailyAt('07:30')
            ->timezone('Europe/Lisbon')
            ->withoutOverlapping();
    })

    ->create();
