<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShortUrlController;
use App\Http\Controllers\UrlDisponiblesController;
use App\Http\Controllers\PruebaController;
use App\Http\Middleware\GoogleAuthMiddleware; // Importar el middleware

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

// Aplicar el middleware 'GoogleAuthMiddleware' a las rutas que deseas proteger
Route::middleware(GoogleAuthMiddleware::class)->group(function () {

    // Rutas para el controlador UserController
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    // Rutas para el controlador UrlDisponiblesController
    Route::get('/urls', [UrlDisponiblesController::class, 'index']);
    Route::post('/urls', [UrlDisponiblesController::class, 'store']);
    Route::get('/urls/{id}', [UrlDisponiblesController::class, 'show']);
    Route::put('/urls/{id}', [UrlDisponiblesController::class, 'update']);
    Route::delete('/urls/{id}', [UrlDisponiblesController::class, 'destroy']);

    // Rutas para el controlador ShortUrlController
    Route::get('/shorturls', [ShortUrlController::class, 'index']);
    Route::post('/shorturls', [ShortUrlController::class, 'store']);
    Route::get('/shorturls/{id}', [ShortUrlController::class, 'show']);
    Route::put('/shorturls/{id}', [ShortUrlController::class, 'update']);
    Route::delete('/shorturls/{id}', [ShortUrlController::class, 'destroy']);

    // Rutas para el controlador PersonaController
    Route::get('/personas', [PersonaController::class, 'index']);
    Route::post('/personas', [PersonaController::class, 'store']);
    Route::get('/personas/{id}', [PersonaController::class, 'show']);
    Route::put('/personas/{id}', [PersonaController::class, 'update']);
    Route::delete('/personas/{id}', [PersonaController::class, 'destroy']);

    
});

// Rutas para el controlador PruebaController
Route::get('/validartokenprueba', [PruebaController::class, 'validar_prueba']);
Route::get('/validartoken', [PruebaController::class, 'validar']);
Route::get('/authgoogle', [PruebaController::class, 'redirectToGoogle']);
Route::get('/refreshtoken',[PruebaController::class,'refreshtoken']);
Route::post('/verifytoken',[PruebaController::class,'validarToken']);