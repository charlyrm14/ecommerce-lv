<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    BrandController,
    CategoryController,
    ProductController
};

Route::prefix('v1/')->group(function () {

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

});
