<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\{
    DashboardController,
    ProductController,
    TankController,
    TransactionController,
    UserController,
    VesselController
};

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
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'auth']
    ],
    function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/tanks/settings', [TankController::class, 'settings'])->name('tanks.settings');
        Route::post('/tanks/{id}/settings', [TankController::class, 'updateSettings'])->name('tanks.updateSettings');
        Route::resource('tanks', TankController::class)->only(['create', 'store', 'edit','destroy']);
        Route::resource('products', ProductController::class);
        Route::resource('vessels', VesselController::class);
        Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

        Route::prefix('clients')->middleware('restrict.to.role:super_admin,client')->group(function () {
            Route::get('/', [UserController::class, 'clientIndex'])->name('clients.index');
            Route::get('/create', [UserController::class, 'clientCreate'])->name('clients.create');
            Route::post('/', [UserController::class, 'clientStore'])->name('clients.store');
            Route::get('/{client}/edit', [UserController::class, 'clientEdit'])->name('clients.edit');
            Route::put('/{client}', [UserController::class, 'clientUpdate'])->name('clients.update');
            Route::delete('/{client}', [UserController::class, 'clientDestroy'])->name('clients.destroy');
        });

        Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
        Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
        Route::get('/profile', [UserController::class, 'profile'])->name('profile');
        Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    }
);

// Auth routes (outside localization)
require __DIR__ . '/auth.php';
