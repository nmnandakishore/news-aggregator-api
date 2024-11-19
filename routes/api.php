<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

    Route::get('/preferences', [UserController::class, 'listPreferences'])->middleware('auth:sanctum');
    Route::post('/preferences', [UserController::class, 'updatePreferences'])->middleware('auth:sanctum');
    Route::delete('/preferences', [UserController::class, 'removePreferences'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->prefix('news')->group(function () {
    Route::get('/all', [NewsController::class, 'listAll']);
    Route::get('/filters', [NewsController::class, 'getFilters']);
    Route::get('/category/{category}', [NewsController::class, 'listFiltered']);
    Route::get('/search', [NewsController::class, 'listFiltered']);
    Route::get('/filtered', [NewsController::class, 'listFiltered']);
    Route::get('/preferred', [NewsController::class, 'listPreferred']);
    Route::get('/{id}', [NewsController::class, 'getById']);

});

//Route::group(['prefix' => 'token', 'middleware' => 'auth:sanctum'], function () {
//    Route::post('/create', function (Request $request) {
//        return '';
//    });
//});
