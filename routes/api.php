<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    CategoryController
};

Route::prefix('v1/')->group(function () {

    Route::prefix('categories/')->controller(CategoryController::class)->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::put('{id}', 'update');
    });

});
