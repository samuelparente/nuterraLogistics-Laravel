<?php
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BonusController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ListController;
use App\Http\Controllers\Admin\ErpController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Auth;


// Rotas login
Route::get('/j1Rs0FsWMuucQI3OOdxv6mLoLXO4cL8yfvoE0sFUscYwVHwAIJbYuByQmqKXRTO2/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/j1Rs0FsWMuucQI3OOdxv6mLoLXO4cL8yfvoE0sFUscYwVHwAIJbYuByQmqKXRTO2/login', [AuthenticatedSessionController::class, 'store']);

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

    // Bonificacoes
    Route::prefix('/backoffice/bonuses')->name('bonuses.')->group(function () {

        // Apenas super-admin e admin podem aceder
        Route::middleware(['role:super-admin|admin'])->group(function () {
            
            // Ver todos
            Route::get('index', [BonusController::class, 'index'])->name('index');

            // Criar 
            Route::get('create', [BonusController::class, 'create'])->name('bonus.create');

            // Guardar 
            Route::post('/store', [BonusController::class, 'store'])->name('bonus.store');

            // Editar um 
            Route::get('{bonus}/edit', [BonusController::class, 'edit'])->name('bonus.edit');

            // Atualizar
            Route::patch('{bonus}/update', [BonusController::class, 'update'])->name('bonus.update');
    
            // Eliminar 
            Route::delete('{bonus}', [BonusController::class, 'destroy'])->name('bonus.destroy');
        });

    });

    // Pedidos a fornecedor
    Route::prefix('/backoffice/orders')->name('orders.')->group(function () {

        // Apenas super-admin e admin podem aceder
        Route::middleware(['role:super-admin|admin'])->group(function () {
            
            // Ver dashboard dos pedidos
            Route::get('dashboard', [OrderController::class, 'dashboard'])->name('dashboard');
            // Ver todos
            Route::get('index', [OrderController::class, 'index'])->name('index');

            // Download de ficheiros de pedidos
            Route::get('download-file', [OrderController::class, 'downloadFile'])->name('downloadFile');

            // Criar pedido vazio
            Route::post('create-empty', [OrderController::class, 'createEmpty'])->name('order.createEmpty');

            // Adicionar items ao pedido
            Route::post('add-items', [OrderController::class, 'addItems'])->name('order.addItems');

            // Pedido de adiçao de item individual com scanner
            Route::post('orders/add-single', [OrderController::class, 'addSingle'])->name('order.addSingle');


            // Retorna a quantidade de items
            Route::get('orders/cart-item-count', [OrderController::class, 'cartItemCount'])->name('orders.order.cartItemCount');

            // Eliminar item do pedido
            Route::delete('/items/{item}', [OrderController::class, 'destroyItem'])->name('order.order_item.destroy');

            // Guardar 
            Route::post('/store', [OrderController::class, 'store'])->name('order.store');

            // Editar 
            Route::get('/edit', [OrderController::class, 'edit'])->name('order.edit');

            // Atualizar
            Route::patch('{order}/update', [OrderController::class, 'update'])->name('order.update');
    
            // Eliminar 
            Route::delete('{order}', [OrderController::class, 'destroy'])->name('order.destroy');
        });

    });

    // Listagens
    Route::prefix('/backoffice/lists')->name('lists.')->group(function () {

        // Apenas super-admin e admin podem aceder
        Route::middleware(['role:super-admin|admin'])->group(function () {
            
            // Ver para pedir em massa
            Route::get('index', [ListController::class, 'index'])->name('index');

            // View para Pedir individual com scanner
            Route::get('single', [ListController::class, 'single'])->name('single');

          
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

    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('/backoffice/erp')->name('erp.')->middleware(['auth', 'role:super-admin|admin'])->group(function () {

        // Buscar produtos filtrados por fornecedor e marca
        Route::get('/products', [ErpController::class, 'getProducts'])->name('products');

    });

});


require __DIR__.'/auth.php';
