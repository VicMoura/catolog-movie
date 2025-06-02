<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FavoriteMovieService;
use App\Http\Requests\FavoritesMovies\CreateFavoritesMoviesRequest;
use App\Http\Requests\FavoritesMovies\ListFavoritesMoviesRequest;
use Illuminate\Http\JsonResponse;

/**
 * Class FavoritesMoviesController
 *
 * Gerencia operações relacionadas aos filmes favoritos dos usuários.
 */
class FavoritesMoviesController extends Controller
{
    protected FavoriteMovieService $favoriteMovieService;

    /**
     * FavoritesMoviesController constructor.
     *
     * @param FavoriteMovieService $favoriteMovieService
     */
    public function __construct(FavoriteMovieService $favoriteMovieService)
    {
        $this->favoriteMovieService = $favoriteMovieService;
    }

    /**
     * Adiciona um filme à lista de favoritos do usuário.
     *
     * @param CreateFavoritesMoviesRequest $request
     * @return JsonResponse
     */
    public function create(CreateFavoritesMoviesRequest $request): JsonResponse
    {
        $tmdbId = $request->input('tmdb_id');
        $user = $request->user();

        $favorites = $this->favoriteMovieService->create($user, $tmdbId);

        return $this->success($favorites, 'Filme favorito adicionado com sucesso!');
    }

    /**
     * Lista os filmes favoritos do usuário, com opção de filtro por gênero.
     *
     * @param ListFavoritesMoviesRequest $request
     * @return JsonResponse
     */
    public function list(ListFavoritesMoviesRequest $request): JsonResponse
    {
        $user = $request->user();

        $movies = $this->favoriteMovieService->list(
            $user, 
            $request->input('genre_id')
        );

        return $this->success($movies, 'Lista de filmes favoritos.');
    }

    /**
     * Remove um filme da lista de favoritos do usuário.
     *
     * @param Request $request
     * @param int $tmdbId
     * @return JsonResponse
     */
    public function delete(Request $request, int $tmdbId): JsonResponse
    {
        $movie = $this->favoriteMovieService->delete($request->user(), $tmdbId);

        return $this->success($movie, 'Filme removido dos favoritos.');
    }
}
