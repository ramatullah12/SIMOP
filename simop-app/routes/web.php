
<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('verified')
        ->name('dashboard');

    Route::middleware('verified')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])
            ->name('profile.edit');

        Route::patch('/profile', [ProfileController::class, 'update'])
            ->name('profile.update');

        Route::delete('/profile', [ProfileController::class, 'destroy'])
            ->name('profile.destroy');

        Route::get('/client/orders', [OrderController::class, 'index'])
            ->name('client.orders.index');

        Route::get('/client/orders/create', [OrderController::class, 'create'])
            ->name('client.orders.create');

        Route::post('/client/orders', [OrderController::class, 'store'])
            ->name('client.orders.store');

        Route::get('/admin/orders', [OrderController::class, 'index'])
            ->name('admin.orders.index');

        Route::get('/orders/{order}', [OrderController::class, 'show'])
            ->name('orders.show');

        Route::patch('/admin/orders/{order}/status', [OrderController::class, 'updateStatus'])
            ->name('admin.orders.status');
    });
});

require __DIR__.'/auth.php';
