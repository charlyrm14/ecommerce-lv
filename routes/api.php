<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    BrandController,
    CartController,
    CategoryController,
    FileController,
    ProductController
};

Route::prefix('v1/')->group(function () {
    
    Route::prefix('auth/')->controller(AuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::post('logout', 'logout')->middleware('auth:api');
    });

    Route::prefix('categories/')->controller(CategoryController::class)->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::put('{id}', 'update');
        Route::get('{slug}', 'show');
        Route::delete('{id}', 'delete');
    });

    Route::prefix('brands/')->controller(BrandController::class)->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::put('{id}', 'update');
        Route::get('{slug}', 'show');
        Route::delete('{id}', 'delete');
    });

    Route::prefix('products/')->controller(ProductController::class)->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::put('{id}', 'update');
        Route::get('{uuid}', 'show');
        Route::delete('{id}', 'delete');
    });

    Route::prefix('carts/')->controller(CartController::class)->group(function () {
        Route::post('', 'store');
    });

    Route::prefix('files/')->controller(FileController::class)->group(function () {
        Route::post('', 'store');
        Route::delete('{id}', 'delete');
    });

});
