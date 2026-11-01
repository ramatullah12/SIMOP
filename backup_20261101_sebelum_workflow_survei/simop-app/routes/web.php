<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FieldDocumentationController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('orders', OrderController::class);

    Route::post('/surveys/{survey}/submit-to-direktur', [SurveyController::class, 'submitToDirektur'])
        ->name('surveys.submit-to-direktur');
    Route::post('/surveys/{survey}/approve-direktur', [SurveyController::class, 'approveByDirektur'])
        ->name('surveys.approve-direktur');
    Route::post('/surveys/{survey}/reject-direktur', [SurveyController::class, 'rejectByDirektur'])
        ->name('surveys.reject-direktur');
    Route::resource('surveys', SurveyController::class);

    Route::resource('projects', ProjectController::class);

    Route::patch('/plannings/{planning}/status', [PlanningController::class, 'updateStatus'])
        ->name('plannings.update-status');
    Route::resource('plannings', PlanningController::class);

    Route::patch('/productions/{production}/progress', [ProductionController::class, 'updateProgress'])
        ->name('productions.update-progress');
    Route::resource('productions', ProductionController::class);

    Route::get('/inventories', [InventoryController::class, 'index'])->name('inventories.index');
    Route::post('/inventories/items', [InventoryController::class, 'storeItem'])->name('inventories.items.store');
    Route::put('/inventories/items/{item}', [InventoryController::class, 'updateItem'])->name('inventories.items.update');
    Route::delete('/inventories/items/{item}', [InventoryController::class, 'destroyItem'])->name('inventories.items.destroy');
    Route::post('/inventories/allocate', [InventoryController::class, 'allocateToProject'])->name('inventories.allocate');
    Route::patch('/inventories/allocations/{projectItem}/return', [InventoryController::class, 'returnStock'])->name('inventories.return');
    Route::delete('/inventories/allocations/{projectItem}', [InventoryController::class, 'destroyAllocation'])->name('inventories.allocations.destroy');

    Route::get('/field-documentations/{field_documentation}/download', [FieldDocumentationController::class, 'download'])
        ->name('field-documentations.download');
    Route::resource('field-documentations', FieldDocumentationController::class)->except(['edit', 'update']);

    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::resource('documents', DocumentController::class)->except(['edit', 'update']);

    Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');
    Route::get('/tracking/{order}', [TrackingController::class, 'show'])->name('tracking.show');

    Route::resource('ratings', RatingController::class)->except(['edit', 'update']);
    Route::resource('employees', EmployeeController::class);

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('vendors', VendorController::class);
    });
});

require __DIR__ . '/auth.php';
