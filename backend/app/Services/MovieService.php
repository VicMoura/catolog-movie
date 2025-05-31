<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\Genre;
use App\Repositories\MovieRepository;
use Exception;

class MovieService
{
    protected MovieRepository $movieRepository;

    /**
     * Injeta a dependência do repositório de filmes.
     *
     * @param MovieRepository $movieRepository
     */
    public function __construct(MovieRepository $movieRepository)
    {
        $this->movieRepository = $movieRepository;
    }

    /**
     * Salva ou atualiza um filme junto com seus gêneros.
     *
     * Recebe os dados do filme (incluindo gêneros) vindos da API TMDB,
     * salva ou atualiza o filme na base local e sincroniza os gêneros.
     *
     * @param array $movieData Dados do filme incluindo o array 'genres'.
     * @return Movie Instância do filme persistida.
     *
     * @throws Exception Em caso de erro na persistência.
     */
    public function storeMovieWithGenres(array $movieData): Movie
    {
        try {
            $movie = $this->movieRepository->storeOrUpdate($movieData);

            foreach ($movieData['genres'] as $genreData) {
                $genre = Genre::updateOrCreate(
                    ['tmdb_genre_id' => $genreData['id']],
                    ['name' => $genreData['name']]
                );

                $movie->genres()->syncWithoutDetaching($genre->id);
            }

            return $movie;
        } catch (Exception $e) {
            throw new Exception('Erro ao salvar o filme e seus gêneros: ' . $e->getMessage());
        }
    }
}
