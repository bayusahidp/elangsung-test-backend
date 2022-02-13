<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/login-admin', [AuthController::class, 'loginadmin']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/csrf', [AuthController::class, 'getcsrftoken']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'user'
], function($router) {
    // admin
    Route::get('/', [UserController::class, 'index']);
    Route::get('/detail/{id}', [UserController::class, 'detail']);
    Route::post('/update/{id}', [UserController::class, 'update']);
    Route::delete('/delete/{id}', [UserController::class, 'destroy']);

    // user
    Route::get('/profile', [UserController::class, 'profile']);
    Route::post('/update-profile', [UserController::class, 'updateprofile']);
    Route::post('/update-foto', [UserController::class, 'updatefoto']);
    Route::post('/change-password', [UserController::class, 'changepassword']);
});

