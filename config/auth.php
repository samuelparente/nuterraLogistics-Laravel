<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuração por defeito de autenticação
    |--------------------------------------------------------------------------
    |
    | Define o guard e broker de passwords por defeito. Podes alterar conforme
    | as necessidades da aplicação. Estas opções são usadas se não especificares
    | explicitamente ao usar Auth::guard() ou Auth::routes().
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Guards de autenticação
    |--------------------------------------------------------------------------
    |
    | Aqui defines todos os guards disponíveis. Um guard representa uma forma
    | de autenticar utilizadores (ex: backoffice ou cliente final).
    | Cada guard usa um provider para buscar o utilizador.
    |
    | Suportado: "session"
    |
    */

    'guards' => [
        // Backoffice
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Providers de utilizadores
    |--------------------------------------------------------------------------
    |
    | Os providers definem como os utilizadores são obtidos da base de dados.
    | Tipicamente é usado Eloquent com um modelo específico.
    | Podes ter múltiplos providers se tiveres utilizadores em tabelas diferentes.
    |
    */

    'providers' => [
        // Funcionários (backoffice, DB central)
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin\User::class,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Reset de passwords
    |--------------------------------------------------------------------------
    |
    | Define como funcionam os resets de password. Cada tipo de utilizador pode
    | ter a sua própria tabela de tokens e tempo de validade.
    |
    */

    'passwords' => [
        // Reset para funcionários (admin)
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],

    
    ],

    /*
    |--------------------------------------------------------------------------
    | Timeout da confirmação de password
    |--------------------------------------------------------------------------
    |
    | Define quantos segundos a confirmação da password é válida antes de pedir
    | nova introdução da senha. Por defeito são 3 horas.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
