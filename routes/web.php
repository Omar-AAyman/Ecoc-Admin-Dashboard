<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\{
    ActivityLogController,
    DashboardController,
    DriverController,
    ProductController,
    SearchController,
    TankController,
    TrailerController,
    TransactionController,
    TruckController,
    UserController,
    VesselController
};
use App\Models\Tank;

// Redirect root URL based on authentication
Route::get('/', function () {
    if (auth()->check()) {
        return redirect(LaravelLocalization::getLocalizedURL(null, route('dashboard')));
    }
    return redirect()->route('login');
});

// Localized routes
Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => [
            'localeSessionRedirect',
            'localizationRedirect',
            'auth',
            'check.user.status',
            'restrict.client.no.tanks',
            'restrict.to.role:super_admin,ceo,client'
        ]
    ],
    function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('tanks', TankController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::get('/tanks/settings', [TankController::class, 'settings'])->name('tanks.settings');
        Route::post('/tanks/{id}/settings', [TankController::class, 'updateSettings'])->name('tanks.updateSettings');
        Route::post('/tanks/{id}/reset', [TankController::class, 'resetTank'])->middleware('restrict.to.role:super_admin,ceo')->name('tanks.reset');
        Route::resource('products', ProductController::class);
        Route::resource('vessels', VesselController::class);

        Route::resource('trucks', TruckController::class);
        Route::resource('trailers', TrailerController::class);
        Route::resource('drivers', DriverController::class);
        Route::resource('users', UserController::class)->middleware('restrict.to.role:super_admin,ceo')->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

        Route::prefix('clients')->middleware('restrict.to.role:super_admin,ceo')->group(function () {
            Route::get('/', [UserController::class, 'clientIndex'])->name('clients.index');
            Route::get('/create', [UserController::class, 'clientCreate'])->name('clients.create');
            Route::post('/', [UserController::class, 'clientStore'])->name('clients.store');
            Route::get('/{client}/edit', [UserController::class, 'clientEdit'])->name('clients.edit');
            Route::put('/{client}', [UserController::class, 'clientUpdate'])->name('clients.update');
            Route::delete('/{client}', [UserController::class, 'clientDestroy'])->name('clients.destroy');
            Route::get('/{client}', [UserController::class, 'clientShow'])->name('clients.show');
        });

        Route::prefix('/transactions')->group(function () {
            Route::get('/', [TransactionController::class, 'index'])->name('transactions.index');
            Route::get('/create', [TransactionController::class, 'create'])->name('transactions.create');
            Route::get('/{id}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
            Route::put('/{id}', [TransactionController::class, 'update'])->name('transactions.update');
            Route::post('/', [TransactionController::class, 'store'])->name('transactions.store');
            Route::get('/{id}', [TransactionController::class, 'showDetails'])->name('transactions.show');
            Route::get('/{id}/duplicate', [TransactionController::class, 'duplicate'])->name('transactions.duplicate');
            Route::delete('/{id}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
        });

        // Profile Management
        Route::prefix('/profile')->group(function () {
            Route::get('/', [UserController::class, 'profile'])->name('profile');
            Route::put('/', [UserController::class, 'updateProfile'])->name('profile.update');
        });

        // Activity logs
        Route::prefix('/activity-logs')->group(function () {
            Route::get('/', [ActivityLogController::class, 'index'])->name('activity-logs.index');
            Route::get('/{id}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
        });

        // Search Routes
        Route::get('/search', [SearchController::class, 'results'])->name('search.results');
    }
);

Route::prefix(LaravelLocalization::setLocale())->group(function () {

    Route::prefix('/api')->middleware(['auth', 'restrict.client.no.tanks', 'restrict.to.role:super_admin,ceo,client'])->group(function () {
        Route::get('/tanks/{id}/company', [TankController::class, 'getCompany']);
        Route::get('/tanks/{id}/product', [TankController::class, 'getProduct']);
        Route::get('/tanks/{id}/capacity', [TankController::class, 'getCapacity']);
        Route::get('/tanks/{id}/details', [TankController::class, 'getDetails']);
        Route::get('/tanks/available', [TankController::class, 'getAvailableTanks']);
        Route::get('/products/{id}', [ProductController::class, 'getProduct'])->name('products.getProduct');
        Route::get('/transactions/statistics', [TransactionController::class, 'statistics'])->name('transactions.statistics');

        Route::get('/search', [SearchController::class, 'ajaxSearch'])->name('search.ajax');
    });
});


// Auth routes (outside localization)
require __DIR__ . '/auth.php';
