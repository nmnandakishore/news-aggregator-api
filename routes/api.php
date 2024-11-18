<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');


Route::get('/check', function (Request $request) {
    return "Response from server";
});

Route::get('/auth-check', function (Request $request) {
    return "Response from server";
})->middleware('auth:sanctum');

Route::group(['prefix' => 'user'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/password/forgot', [AuthController::class, 'sendPasswordResetLink']);
    Route::post('/password/reset', [AuthController::class, 'resetPassword']);
});

Route::middleware('auth:sanctum')->prefix('news')->group(function () {
    Route::get('/all', [NewsController::class, 'listAll']);
    Route::get('/category/{category}', [NewsController::class, 'filter']);
    Route::get('/search', [NewsController::class, 'filter']);
    Route::get('/filter', [NewsController::class, 'filter']);
    Route::get('/{id}', [NewsController::class, 'getById']);

//    TODO: Create route to list news based on user preference

});

//Route::group(['prefix' => 'token', 'middleware' => 'auth:sanctum'], function () {
//    Route::post('/create', function (Request $request) {
//        return '';
//    });
//});
