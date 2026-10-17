
<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SurveyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('verified')
        ->name('dashboard');

    Route::middleware('verified')->group(function () {
        // Profile
        Route::get('/profile', [ProfileController::class, 'edit'])
            ->name('profile.edit');

        Route::patch('/profile', [ProfileController::class, 'update'])
            ->name('profile.update');

        Route::delete('/profile', [ProfileController::class, 'destroy'])
            ->name('profile.destroy');

        // Order & RFQ - Client
        Route::get('/client/orders', [OrderController::class, 'index'])
            ->name('client.orders.index');

        Route::get('/client/orders/create', [OrderController::class, 'create'])
            ->name('client.orders.create');

        Route::post('/client/orders', [OrderController::class, 'store'])
            ->name('client.orders.store');

        // Order & RFQ - Admin
        Route::get('/admin/orders', [OrderController::class, 'index'])
            ->name('admin.orders.index');

        Route::get('/orders/{order}', [OrderController::class, 'show'])
            ->name('orders.show');

        Route::patch('/admin/orders/{order}/status', [OrderController::class, 'updateStatus'])
            ->name('admin.orders.status');

        // Manajemen Client
        Route::prefix('admin/clients')
            ->name('admin.clients.')
            ->controller(ClientController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{client}', 'show')->name('show');
                Route::put('/{client}', 'update')->name('update');
            });

        // Manajemen Survey
        Route::prefix('admin/surveys')
            ->name('admin.surveys.')
            ->controller(SurveyController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{survey}', 'show')->name('show');
                Route::put('/{survey}', 'update')->name('update');
            });
    });
});

require __DIR__.'/auth.php';
