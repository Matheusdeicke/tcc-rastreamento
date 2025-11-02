<?php

use Illuminate\Support\Facades\Route;            // <-- garante a facade
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Cme\OrderCmeController;
use App\Http\Controllers\Cme\KitController;
use App\Http\Controllers\Cme\KitInstanceController;
use Illuminate\Support\Facades\Auth;

Route::middleware(['auth'])->group(function () {
    // Enfermagem
    Route::resource('orders', OrderController::class)->only(['index','create','store','show']);

    // CME
    Route::prefix('cme')->middleware('role:cme|admin')->group(function () {
        Route::get('pedidos', [OrderCmeController::class,'index'])->name('cme.orders');
        Route::post('pedidos/{order}/aceitar', [OrderCmeController::class,'accept'])->name('cme.orders.accept');
        Route::post('pedidos/{order}/recusar', [OrderCmeController::class,'reject'])->name('cme.orders.reject');
        Route::post('pedidos/{order}/preparo', [OrderCmeController::class,'startPrep'])->name('cme.orders.prep');
        Route::post('pedidos/{order}/pronto', [OrderCmeController::class,'ready'])->name('cme.orders.ready');
        Route::post('pedidos/{order}/entregar', [OrderCmeController::class,'deliver'])->name('cme.orders.deliver');
        Route::post('pedidos/{order}/fechar', [OrderCmeController::class,'close'])->name('cme.orders.close');
        Route::get('pedidos/{order}', [OrderCmeController::class,'show'])->name('cme.orders.show');
        Route::resource('kits', KitController::class); // index, create, store, show, edit, update, destroy
        Route::get('kits/{kit}/instancias/create', [KitInstanceController::class,'create'])->name('kits.instances.create');
        Route::post('kits/{kit}/instancias', [KitInstanceController::class,'store'])->name('kits.instances.store');
        Route::get('instancias/{instance}/edit', [KitInstanceController::class,'edit'])->name('instances.edit');
        Route::put('instancias/{instance}', [KitInstanceController::class,'update'])->name('instances.update');
        Route::delete('instancias/{instance}', [KitInstanceController::class,'destroy'])->name('instances.destroy');
    });
});


    Route::get('/', function () {
        if (!Auth::check()) {
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

        return redirect('/login');
    });


require __DIR__.'/auth.php';                     // <-- ESSENCIAL
