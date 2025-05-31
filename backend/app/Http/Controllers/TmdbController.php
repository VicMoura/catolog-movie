<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TmdbService;

class TmdbController extends Controller
{
    protected TmdbService $tmdbService;

    /**
     * Injeta a dependência do serviço TMDB via construtor.
     *
     * @param TmdbService $tmdbService Serviço responsável pela comunicação com a API TMDB.
     */
    public function __construct(TmdbService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    /**
     * Endpoint para busca de filmes com base em uma query.
     *
     * Valida a requisição para garantir que a query existe e tem no mínimo 2 caracteres.
     * Retorna os resultados da busca via JSON.
     *
     * @param Request $request Requisição HTTP contendo o parâmetro 'query'.
     * @return \Illuminate\Http\JsonResponse Resposta JSON com os dados dos filmes encontrados.
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2',
        ]);

        $query = $validated['query'];

        $movies = $this->tmdbService->searchMovies($query);
        return $this->success($movies, 'Filme encontrado com sucesso', 200);
    }
}
