<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// use App\Http\Controllers\ProductController;
// use App\Http\Controllers\SearchController;
// use App\Http\Controllers\TankController;
// use App\Http\Controllers\TransactionController;
// use Illuminate\Support\Facades\Route;



// Route::prefix('/api')->middleware(['auth', 'restrict.client.no.tanks', 'restrict.to.role:super_admin,client'])->group(function () {
//     Route::prefix('/tanks')->group(function () {
//         Route::get('/{id}/company', [TankController::class, 'getCompany']);
//         Route::get('/{id}/product', [TankController::class, 'getProduct']);
//         Route::get('/{id}/capacity', [TankController::class, 'getCapacity']);
//         Route::get('/{id}/details', [TankController::class, 'getDetails']);
//         Route::get('/available', [TankController::class, 'getAvailableTanks']);
//     });

//     Route::get('/products/{id}', [ProductController::class, 'getProduct'])->name('products.getProduct');
//     Route::get('/transactions/statistics', [TransactionController::class, 'statistics'])->name('transactions.statistics');

//     Route::get('/search', [SearchController::class, 'ajaxSearch'])->name('search.ajax');
// });
