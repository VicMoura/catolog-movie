<?php

namespace App\Repository;

use App\Models\Movie;

class MovieRepository
{
    /**
     * Cria um novo filme ou atualiza um existente baseado no TMDB ID.
     *
     * @param array $data Dados do filme vindos da API TMDB.
     * @return Movie Instância do filme criada ou atualizada.
     */
    public function storeOrUpdate(array $data): Movie
    {
        return Movie::updateOrCreate(
            ['tmdb_id' => $data['id']], // Critério de busca para update ou criação
            [
                'title'        => $data['title'],
                'overview'     => $data['overview'],
                'poster_path'  => $data['poster_path'] ?? null,
                'release_date' => !empty($data['release_date']) ? $data['release_date'] : null,
            ]
        );
    }

    /**
     * Busca um filme pelo TMDB ID.
     *
     * @param int $tmdbId Identificador do filme na TMDB.
     * @return Movie|null Retorna o filme encontrado ou null se não existir.
     */
    public function findByTmdbId(int $tmdbId): ?Movie
    {
        return Movie::where('tmdb_id', $tmdbId)->first();
    }
}
