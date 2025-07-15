<?php
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Auth;


// Rotas login
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

// Backoffice 
Route::middleware('auth')->group(function () {

    // Painel admin dashboard
    Route::get('/backoffice/dashboard', function () {
        return view('layouts.admin.dashboard.dashboard_admin', ['user' => Auth::user()]);
    })->name('backoffice.dashboard');

    // Users
    Route::prefix('/backoffice/users')->name('users.')->group(function () {

        // Apenas super-admin e admin podem aceder
        Route::middleware(['role:super-admin|admin'])->group(function () {
            
            // Ver todos os utilizadores
            Route::get('index', [UserController::class, 'index'])->name('index');

            // Criar utilizador
            Route::get('create', [UserController::class, 'create'])->name('user.create');

            // Guardar utilizador
            Route::post('/store', [UserController::class, 'store'])->name('user.store');

            // Editar um utilizador
            Route::get('{user}/edit', [UserController::class, 'edit'])->name('user.edit');

            // Atualizar um utilizador
            Route::patch('{user}/update', [UserController::class, 'update'])->name('user.update');
    
            // Eliminar um utilizador
            Route::delete('{user}', [UserController::class, 'destroy'])->name('user.destroy');
        });

    });

   
    // Profile
    Route::prefix('/backoffice/profile')->name('profiles.')->group(function () {

        // Apenas super-admin,admin e gestor  podem aceder. Na versão seguinte talvez deixar externos aceder.
        Route::middleware(['role:super-admin|admin|gestor'])->group(function () {
            
            // Ver perfil
            Route::get('{user}/show', [ProfileController::class, 'show'])->name('profile.show');

            // Editar perfil
            Route::get('{user}/edit', [ProfileController::class, 'edit'])->name('profile.edit');

            // Atualizar perfil
            Route::patch('{user}/update', [ProfileController::class, 'update'])->name('profile.update');
    
        });

    });

    // Logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';
