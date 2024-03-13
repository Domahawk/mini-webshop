<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthenticationController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/filter', [ProductController::class, 'filterProducts']);
    Route::get('/products/{product}', [ProductController::class, 'show']);

    Route::get('/categories/{category}/products', [CategoryController::class, 'showCategoryProducts']);

    Route::get('/logout', [AuthenticationController::class, 'logout']);

    Route::post('/orders', [OrderController::class, 'store']);
});
