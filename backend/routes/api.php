<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TmdbController;
use App\Http\Controllers\FavoritesMoviesController;
use App\Http\Controllers\GenreController;

// Rotas públicas para autenticação
Route::prefix('users')->group(function () {
    Route::post('', [UserController::class, 'create']); 
    Route::post('/login', [UserController::class, 'login']); 
});

// Rotas protegidas por Sanctum
Route::middleware('auth:sanctum')->group(function () {
    
    // Info do usuário logado
    Route::prefix('users')->group(function () {
        Route::get('/detail', [UserController::class, 'detail']);
        Route::post('/logout', [UserController::class, 'logout']);
    });
    
    // Busca filmes TMDB
    Route::get('/movies/search', [TmdbController::class, 'search']);
    Route::get('/movies/{tmdbId}/detail', [TmdbController::class, 'detail']); 

    // Gêneros
    Route::get('/genres', [GenreController::class, 'index']);

    // Favoritos de filmes
    Route::prefix('favorites')->group(function () {
        Route::post('/', [FavoritesMoviesController::class, 'create']);
        Route::get('/', [FavoritesMoviesController::class, 'list']);
        Route::delete('/{movie}', [FavoritesMoviesController::class, 'delete']);
    });
});

