<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReturnController;

use App\Http\Controllers\Cme\OrderCmeController;
use App\Http\Controllers\Cme\KitController;
use App\Http\Controllers\Cme\KitInstanceController;
use App\Http\Controllers\Cme\ReturnCmeController;
use App\Http\Controllers\Cme\KitItemController;

/*
|--------------------------------------------------------------------------
| Rotas de ENFERMAGEM
|--------------------------------------------------------------------------
|
| Aqui ficam as rotas que a enfermagem usa no dia a dia:
| - Solicitar kits (orders)
| - Criar e visualizar devoluções
|
| Admin também pode acessar tudo isso.
|
*/

Route::middleware(['auth', 'role:enfermagem|admin'])->group(function () {
    // Pedidos da enfermagem
    Route::resource('orders', OrderController::class)
        ->only(['index', 'create', 'store', 'show']);

    // Devoluções criadas pela enfermagem
    Route::prefix('devolucoes')->group(function () {
        Route::get('/', [ReturnController::class, 'index'])->name('returns.index');
        Route::get('/criar', [ReturnController::class, 'create'])->name('returns.create');
        Route::post('/', [ReturnController::class, 'store'])->name('returns.store');
        Route::get('/{returnRequest}', [ReturnController::class, 'show'])->name('returns.show');
    });
});

/*
|--------------------------------------------------------------------------
| Rotas da CME
|--------------------------------------------------------------------------
|
| Tudo que é da Central de Material e Esterilização:
| - Gerenciar pedidos (aceitar, recusar, preparar, entregar, fechar)
| - Gerenciar kits e suas peças
| - Gerenciar instâncias de kits
| - Tratar devoluções na visão CME
|
| Apenas usuários com papel "cme" ou "admin" acessam.
|
*/

Route::prefix('cme')
    ->middleware(['auth', 'role:cme|admin'])
    ->group(function () {
        // Pedidos na visão da CME
        Route::get('pedidos', [OrderCmeController::class, 'index'])->name('cme.orders');
        Route::get('pedidos/{order}', [OrderCmeController::class, 'show'])->name('cme.orders.show');
        Route::post('pedidos/{order}/aceitar', [OrderCmeController::class, 'accept'])->name('cme.orders.accept');
        Route::post('pedidos/{order}/recusar', [OrderCmeController::class, 'reject'])->name('cme.orders.reject');
        Route::post('pedidos/{order}/preparo', [OrderCmeController::class, 'startPrep'])->name('cme.orders.prep');
        Route::post('pedidos/{order}/pronto', [OrderCmeController::class, 'ready'])->name('cme.orders.ready');
        Route::post('pedidos/{order}/entregar', [OrderCmeController::class, 'deliver'])->name('cme.orders.deliver');
        Route::post('pedidos/{order}/fechar', [OrderCmeController::class, 'close'])->name('cme.orders.close');

        // Kits (CRUD completo)
        Route::resource('kits', KitController::class);

        // Peças de um kit
        Route::post('kits/{kit}/items', [KitItemController::class, 'storeMany'])->name('kits.items.storeMany');
        Route::get('kits/{kit}/items/{item}/edit', [KitItemController::class, 'edit'])->name('kits.items.edit');
        Route::put('kits/{kit}/items/{item}', [KitItemController::class, 'update'])->name('kits.items.update');
        Route::delete('kits/{kit}/items/{item}', [KitItemController::class, 'destroy'])->name('kits.items.destroy');

        // Instâncias individuais de kits
        Route::get('kits/{kit}/instancias/create', [KitInstanceController::class, 'create'])->name('kits.instances.create');
        Route::post('kits/{kit}/instancias', [KitInstanceController::class, 'store'])->name('kits.instances.store');
        Route::get('instancias/{instance}/edit', [KitInstanceController::class, 'edit'])->name('instances.edit');
        Route::put('instancias/{instance}', [KitInstanceController::class, 'update'])->name('instances.update');
        Route::delete('instancias/{instance}', [KitInstanceController::class, 'destroy'])->name('instances.destroy');

        Route::post('kits/{kit}/instancias/bulk', [KitController::class, 'storeInstances'])
            ->name('kits.instances.bulk-store');

        // Devoluções na visão da CME
        Route::get('devolucoes', [ReturnCmeController::class, 'index'])->name('cme.returns');
        Route::get('devolucoes/{returnRequest}', [ReturnCmeController::class, 'show'])->name('cme.returns.show');
        Route::post('devolucoes/{returnRequest}/confirmar-recebimento', [ReturnCmeController::class, 'confirmReceipt'])->name('cme.returns.confirm');
        // Route::post('devolucoes/{returnRequest}/quarentena', [ReturnCmeController::class, 'sendToQuarantine'])->name('cme.returns.quarantine');
        Route::post('devolucoes/{returnRequest}/reprocessar', [ReturnCmeController::class, 'sendToReprocess'])->name('cme.returns.reprocess');
        Route::post('devolucoes/{returnRequest}/liberar', [ReturnCmeController::class, 'releaseToStock'])->name('cme.returns.release');
        Route::post('devolucoes/{returnRequest}/conferencia-itens', [ReturnCmeController::class, 'checkItems'])
            ->name('cme.returns.check-items');
    });

/*
|--------------------------------------------------------------------------
| Rota inicial (home)
|--------------------------------------------------------------------------
|
| Define para onde cada usuário vai ao acessar "/":
| - admin  -> /cme/kits
| - cme    -> /cme/pedidos
| - enferm -> /orders
|
| Usuário sem role não deveria existir: forçam logout e mostra erro.
|
*/

Route::get('/', function () {
    if (! Auth::check()) {
        return redirect('/login');
    }

    $user = Auth::user();

    if ($user->hasRole('admin')) {
        return redirect('/cme/kits');
    } elseif ($user->hasRole('cme')) {
        return redirect('/cme/pedidos');
    } elseif ($user->hasRole('enfermagem')) {
        return redirect('/orders');
    }

    // Usuário autenticado mas sem papel: algo errado na configuração
    Auth::logout();

    return redirect('/login')->withErrors([
        'email' => 'Sua conta não possui um perfil válido. Contate o administrador.',
    ]);
});

require __DIR__ . '/auth.php';
