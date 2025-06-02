<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TmdbService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.tmdb.base_url');
        $this->apiKey = config('services.tmdb.api_key');
    }

    /**
     * Busca filmes por uma query de texto.
     *
     * Utiliza cache para armazenar resultados da busca por 1 hora,
     * reduzindo chamadas repetidas à API externa.
     *
     * @param string $query Texto para busca de filmes.
     * @return array|null Retorna array com dados da busca ou null em caso de falha.
     */
    public function searchMovies(string $query)
    {
        return Cache::remember(
            "tmdb_search_" . md5($query),
            now()->addHour(),
            function () use ($query) {
                $response = Http::get("{$this->baseUrl}/search/movie", [
                    'api_key'  => $this->apiKey,
                    'query'    => $query,
                    'language' => 'pt-BR',
                ]);

                return $response->successful() ? $response->json() : null;
            }
        );
    }

    /**
     * Obtém detalhes completos de um filme pelo TMDB ID.
     *
     * Os detalhes são armazenados em cache por 6 horas, pois raramente mudam.
     *
     * @param int $tmdbId Identificador do filme na TMDB.
     * @return array|null Retorna os dados do filme ou null em caso de erro.
     */
    public function getMovieDetails(int $tmdbId)
    {
        return Cache::remember(
            "tmdb_movie_{$tmdbId}",
            now()->addHours(6),
            function () use ($tmdbId) {
                $response = Http::get("{$this->baseUrl}/movie/{$tmdbId}", [
                    'api_key'  => $this->apiKey,
                    'language' => 'pt-BR',
                ]);

                return $response->successful() ? $response->json() : null;
            }
        );
    }

    /**
     * Pega os trailers e vídeos relacionados ao filme.
     */
    public function getMovieVideos(int $tmdbId)
    {
        return Cache::remember(
            "tmdb_movie_videos_{$tmdbId}",
            now()->addHours(6),
            function () use ($tmdbId) {
                $response = Http::get("{$this->baseUrl}/movie/{$tmdbId}/videos", [
                    'api_key'  => $this->apiKey,
                    'language' => 'pt-BR',
                ]);

                return $response->successful() ? $response->json()['results'] : null;
            }
        );
    }

    /**
     *  Pega os atores (elenco) e equipe técnica do filme.
     */
    public function getMovieCredits(int $tmdbId)
    {
        return Cache::remember(
            "tmdb_movie_credits_{$tmdbId}",
            now()->addHours(6),
            function () use ($tmdbId) {
                $response = Http::get("{$this->baseUrl}/movie/{$tmdbId}/credits", [
                    'api_key'  => $this->apiKey,
                    'language' => 'pt-BR',
                ]);

                return $response->successful() ? $response->json() : null;
            }
        );
    }

    /**
     *  Método utilitário para trazer tudo junto: detalhes, trailers e elenco.
     */
    public function getFullMovieData(int $tmdbId)
    {
        return [
            'details' => $this->getMovieDetails($tmdbId),
            'videos'  => $this->getMovieVideos($tmdbId),
            'credits' => $this->getMovieCredits($tmdbId),
        ];
    }
}
