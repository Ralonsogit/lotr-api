<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CharacterController;
use App\Http\Controllers\Api\V1\EquipmentController;
use App\Http\Controllers\Api\V1\FactionController;

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

// Api V1
Route::prefix('v1')->group(function() {
    // Auth
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    // Add Middlewares
    Route::prefix('admin')->middleware(['auth:sanctum', 'checkAdmin'])->group(function () {
        // Factions
        Route::post('/factions', [FactionController::class, 'store']);
        Route::put('/factions/{id}', [FactionController::class, 'update']);
        Route::delete('/factions/{id}', [FactionController::class, 'destroy']);
        Route::post('/factions/{id}/restore', [FactionController::class, 'restore']);
        Route::delete('/factions/{id}/force', [FactionController::class, 'forceDelete']);

        // Equipments
        Route::post('/equipments', [EquipmentController::class, 'store']);
        Route::put('/equipments/{id}', [EquipmentController::class, 'update']);
        Route::delete('/equipments/{id}', [EquipmentController::class, 'destroy']);
        Route::post('/equipments/{id}/restore', [FactionController::class, 'restore']);
        Route::delete('/equipments/{id}/force', [FactionController::class, 'forceDelete']);

        // Characters
        Route::post('/characters', [CharacterController::class, 'store']);
        Route::put('/characters/{id}', [CharacterController::class, 'update']);
        Route::delete('/characters/{id}', [CharacterController::class, 'destroy']);
        Route::post('/characters/{id}/restore', [FactionController::class, 'restore']);
        Route::delete('/characters/{id}/force', [FactionController::class, 'forceDelete']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::post('/logout', [AuthController::class, 'logout']);

        // Factions
        Route::get('/factions', [FactionController::class, 'index']);
        Route::get('/factions/{id}', [FactionController::class, 'show']);

        // Equipments
        Route::get('/equipments', [EquipmentController::class, 'index']);
        Route::get('/equipments/{id}', [EquipmentController::class, 'show']);

        // Characters
        Route::get('/characters', [CharacterController::class, 'index']);
        Route::get('/characters/{id}', [CharacterController::class, 'show']);
    });
});
