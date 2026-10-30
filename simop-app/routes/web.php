<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\BappController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
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

        Route::prefix('productions')
            ->name('productions.')
            ->controller(ProductionController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{production}', 'show')->name('show');

                Route::patch(
                    '/{production}/progress',
                    'updateProgress'
                )->name('progress');

                Route::patch(
                    '/{production}/status',
                    'updateStatus'
                )->name('status');
            });

        Route::prefix('bapps')
            ->name('bapps.')
            ->controller(BappController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{bapp}', 'show')->name('show');

                Route::patch(
                    '/{bapp}/status',
                    'updateStatus'
                )->name('status');
            });

        Route::prefix('documents')
            ->name('documents.')
            ->controller(DocumentController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');

                Route::get(
                    '/{document}/download',
                    'download'
                )->name('download');

                Route::get(
                    '/{document}',
                    'show'
                )->name('show');

                Route::delete(
                    '/{document}',
                    'destroy'
                )->name('destroy');
            });

        Route::prefix('invoices')
            ->name('invoices.')
            ->controller(InvoiceController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{invoice}', 'show')->name('show');

                Route::patch(
                    '/{invoice}/status',
                    'updateStatus'
                )->name('status');
            });

        Route::get(
            '/admin/ratings',
            [RatingController::class, 'index']
        )->name('admin.ratings.index');

        Route::prefix('client/ratings')
            ->name('client.ratings.')
            ->controller(RatingController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');

                Route::get(
                    '/projects/{project}/create',
                    'create'
                )->name('create');

                Route::post(
                    '/projects/{project}',
                    'store'
                )->name('store');
            });

        Route::get(
            '/ratings/{rating}',
            [RatingController::class, 'show']
        )->name('ratings.show');

        Route::prefix('client/tracking')
            ->name('client.tracking.')
            ->controller(TrackingController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{project}', 'show')->name('show');
            });

        Route::get(
            '/admin/reports',
            [ReportController::class, 'index']
        )->name('admin.reports.index');

        Route::get(
            '/direktur/reports',
            [ReportController::class, 'index']
        )->name('direktur.reports.index');

        Route::prefix('admin/vendors')
            ->name('admin.vendors.')
            ->controller(VendorController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{vendor}', 'show')->name('show');
                Route::get('/{vendor}/edit', 'edit')->name('edit');
                Route::put('/{vendor}', 'update')->name('update');
            });

        Route::prefix('admin/users')
            ->name('admin.users.')
            ->controller(UserController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');

                Route::get(
                    '/create',
                    'create'
                )->name('create');

                Route::post(
                    '/',
                    'store'
                )->name('store');

                Route::patch(
                    '/{user}/verification',
                    'updateVerification'
                )->name('verification');

                Route::get(
                    '/{user}',
                    'show'
                )->name('show');

                Route::get(
                    '/{user}/edit',
                    'edit'
                )->name('edit');

                Route::put(
                    '/{user}',
                    'update'
                )->name('update');
            });
    });
});

require __DIR__.'/auth.php';