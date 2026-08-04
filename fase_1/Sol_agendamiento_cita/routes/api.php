<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TipoCitaController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\UsuarioController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rutas públicas
Route::post('/login', [AuthController::class, 'login']);
Route::put('/usuarios/password', [AuthController::class, 'updatePassword']);
Route::post('/hash-password', [AuthController::class, 'hashPassword']);

// Rutas protegidas
Route::middleware(['auth.token'])->group(function () {
    // Usuarios
    Route::get('/usuarios', [UsuarioController::class, 'index']);
    Route::post('/usuarios', [UsuarioController::class, 'store']);
    Route::get('/usuarios/{id}', [UsuarioController::class, 'show']);
    Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);
    
    // Tipos de Cita
    Route::get('/tipos-cita', [TipoCitaController::class, 'index']);
    Route::post('/tipos-cita', [TipoCitaController::class, 'store']);
    Route::put('/tipos-cita/{id}', [TipoCitaController::class, 'update']);
    Route::delete('/tipos-cita/{id}', [TipoCitaController::class, 'destroy']);
    
    // Citas
    Route::get('/citas', [CitaController::class, 'index']);
    Route::get('/citas/dashboard', [CitaController::class, 'dashboard']);
    Route::get('/usuarios/{userId}/citas', [CitaController::class, 'citasPorUsuario']);
    Route::post('/citas', [CitaController::class, 'store']);
    Route::get('/citas/{id}', [CitaController::class, 'show']);
    Route::put('/citas/{id}', [CitaController::class, 'update']);
    Route::put('/citas/{id}/estado', [CitaController::class, 'updateEstado']);
    Route::delete('/citas/{id}', [CitaController::class, 'destroy']);
});

