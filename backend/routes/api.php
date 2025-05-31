<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TmdbController;
use App\Http\Controllers\FavoritesMoviesController;

/**
 * Rotas públicas
 */
Route::post('/register', [UserController::class, 'create']);
Route::post('/login', [UserController::class, 'login']);

/**
 * Rotas protegidas (autenticadas)
 */
Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/detail', [UserController::class, 'detail']);
    Route::post('/logout', [UserController::class, 'logout']);

    Route::get('/tmdb/search', [TmdbController::class, 'search']);

    Route::prefix('favorites')->group(function () {
        Route::post('/', [FavoritesMoviesController::class, 'create']);
        Route::get('/', [FavoritesMoviesController::class, 'list']);
        Route::delete('/{movie}', [FavoritesMoviesController::class, 'delete']);
    });
});
