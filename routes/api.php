<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ZoneController;
use App\Http\Controllers\Api\V1\VehicleController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\ParkingController;

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

Route::post('auth/register', RegisterController::class);
Route::post('auth/login', LoginController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('vehicles', VehicleController::class);

    Route::get('zones', [ZoneController::class, 'index']);
    Route::get('zones/{zone}', [ZoneController::class, 'show']);

    Route::get('parkings', [ParkingController::class, 'index']);
    Route::get('parkings/{parking}', [ParkingController::class, 'show']);
    Route::post('parkings/start', [ParkingController::class, 'start']);
    Route::put('parkings/stop/{parking}', [ParkingController::class, 'stop']);
});
