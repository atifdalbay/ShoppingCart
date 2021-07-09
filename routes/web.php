<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function (){

    Route::post('logout', [\App\Http\Controllers\AuthController::class,'logout']);

    Route::prefix('products')->group(function (){

        Route::get('/',[\App\Http\Controllers\ProductController::class,'get']);
        Route::get('/{id}',[\App\Http\Controllers\ProductController::class,'getById']);
        Route::post('create',[\App\Http\Controllers\ProductController::class,'create']);
        Route::put('update/{id}',[\App\Http\Controllers\ProductController::class,'update']);
        Route::delete('delete/{id}',[\App\Http\Controllers\ProductController::class,'delete']);

    });

    Route::prefix('basket')->group(function (){

        Route::get('/',[\App\Http\Controllers\ShoppingCartController::class,'get']);
        Route::post('create',[\App\Http\Controllers\ShoppingCartController::class,'create']);
        Route::put('update/{id}',[\App\Http\Controllers\ShoppingCartController::class,'update']);
        Route::delete('discharge/{id?}',[\App\Http\Controllers\ShoppingCartController::class,'discharge']);
    });

    Route::get('profile',[\App\Http\Controllers\UserController::class,'profile']);
});

Route::post('login', [\App\Http\Controllers\AuthController::class,'login']);
Route::post('register',[\App\Http\Controllers\UserController::class,'register']);
