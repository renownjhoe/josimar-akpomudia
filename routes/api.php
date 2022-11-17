<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\DroneController;
use App\Http\Controllers\LoaderController;
use App\Http\Controllers\ProductsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('register', [AuthController::class, 'create']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user()->load('account_type');
});

Route::middleware(['auth:sanctum'])->prefix('drone')->controller(DroneController::class)->group(function(){
    Route::get('/', 'index');
    Route::get('/{drone}', 'show');
    Route::post('/create', 'store');
    Route::get('/battery/{drone}', 'battery_level');
    Route::get('/state/{drone}', 'drone_state');
});

Route::middleware(['auth:sanctum'])->prefix('load')->controller(LoaderController::class)->group(function(){
    Route::get('/', 'index');
    Route::post('/drone', 'store');
    Route::get('/{drone}', 'check_drone');
});

Route::middleware(['auth:sanctum'])->get('/available/drones', [DroneController::class, 'available_drones']);

Route::middleware(['auth:sanctum'])->prefix('product')->controller(ProductsController::class)->group(function(){
    Route::get('/', 'index');
    Route::get('/{drone}', 'show');
    Route::post('/create', 'store');
});
