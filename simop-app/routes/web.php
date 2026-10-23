<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\WorkEstimateController;
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

        Route::prefix('admin/work-estimates')
            ->name('admin.work-estimates.')
            ->controller(WorkEstimateController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{workEstimate}', 'show')->name('show');
            });

        Route::prefix('approvals')
            ->name('approvals.')
            ->controller(ApprovalController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');

                Route::post(
                    '/work-estimates/{workEstimate}',
                    'store'
                )->name('store');

                Route::patch(
                    '/{approval}/decide',
                    'decide'
                )->name('decide');
            });

        Route::prefix('projects')
            ->name('projects.')
            ->controller(ProjectController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{project}', 'show')->name('show');

                Route::patch(
                    '/{project}/progress',
                    'updateProgress'
                )->name('progress');

                Route::patch(
                    '/{project}/status',
                    'updateStatus'
                )->name('status');
            });

        Route::prefix('plannings')
            ->name('plannings.')
            ->controller(PlanningController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{planning}', 'show')->name('show');

                Route::patch(
                    '/{planning}/status',
                    'updateStatus'
                )->name('status');
            });
    });
});

require __DIR__.'/auth.php';