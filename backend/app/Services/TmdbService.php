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
        // Configura as URLs base e a chave de API do TMDB a partir das configurações do Laravel
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
            "tmdb_search_" . md5($query), // Chave única baseada na query (hash MD5)
            now()->addHour(),             // Tempo de cache: 1 hora
            function () use ($query) {
                $response = Http::get("{$this->baseUrl}/search/movie", [
                    'api_key'  => $this->apiKey,
                    'query'    => $query,
                    'language' => 'pt-BR',
                ]);

                if ($response->successful()) {
                    return $response->json();
                }

                return null;
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
            "tmdb_movie_{$tmdbId}",     // Chave única baseada no ID do filme
            now()->addHours(6),         // Tempo de cache: 6 horas
            function () use ($tmdbId) {
                $response = Http::get("{$this->baseUrl}/movie/{$tmdbId}", [
                    'api_key'  => $this->apiKey,
                    'language' => 'pt-BR',
                ]);

                if ($response->successful()) {
                    return $response->json();
                }

                return null;
            }
        );
    }
}
