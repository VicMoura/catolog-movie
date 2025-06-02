<?php

namespace App\Services;

use App\Models\User;
use App\Models\Movie;
use App\Services\TmdbService;
use App\Services\MovieService;
use App\Repository\FavoriteMovieRepository;
use App\Repository\MovieRepository;
use Exception;
use Illuminate\Support\Collection;

class FavoriteMovieService
{
    protected TmdbService $tmdbService;
    protected MovieService $movieService;
    protected MovieRepository $movieRepository;
    protected FavoriteMovieRepository $favoriteMovieRepository;

    /**
     * Injeta as dependências necessárias para a manipulação de filmes favoritos.
     */
    public function __construct(
        TmdbService $tmdbService,
        MovieService $movieService,
        MovieRepository $movieRepository,
        FavoriteMovieRepository $favoriteMovieRepository
    ) {
        $this->tmdbService = $tmdbService;
        $this->movieService = $movieService;
        $this->movieRepository = $movieRepository;
        $this->favoriteMovieRepository = $favoriteMovieRepository;
    }

    /**
     * Adiciona um filme aos favoritos do usuário, buscando detalhes na TMDB,
     * armazenando o filme localmente e associando ao usuário.
     *
     * @param User $user Usuário que deseja favoritar o filme.
     * @param int  $tmdbId ID do filme na API TMDB.
     * @return array Dados do filme favoritado formatados.
     *
     * @throws Exception Em caso de erro na busca ou persistência.
     */
    public function create(User $user, int $tmdbId): array
    {
        try {
            $movieData = $this->tmdbService->getMovieDetails($tmdbId);

            if (!$movieData) {
                throw new Exception('Filme não encontrado na API TMDB.');
            }

            $movie = $this->movieService->storeMovieWithGenres($movieData);

            $this->favoriteMovieRepository->addFavorite($user, $movie);

            return $this->formatMovieResponse($movie);

        } catch (Exception $e) {
            throw new Exception('Não foi possível adicionar o filme. Erro: ' . $e->getMessage());
        }
    }

    /**
     * Lista os filmes favoritos do usuário, opcionalmente filtrados por gênero.
     *
     * @param User     $user    Usuário cujos favoritos serão listados.
     * @param int|null $genreId (Opcional) ID do gênero para filtro.
     * @return Collection Lista dos filmes favoritos.
     */
    public function list(User $user, ?int $genreId = null): Collection
    {
        return $this->favoriteMovieRepository->listFavorites($user, $genreId);
    }

    /**
     * Remove um filme dos favoritos do usuário.
     *
     * @param User $user Usuário que deseja remover o filme.
     * @param int  $tmdbId ID do filme na API TMDB.
     * @return array Dados do filme removido formatados.
     *
     * @throws Exception Se o filme não existir ou não estiver nos favoritos.
     */
    public function delete(User $user, int $tmdbId): array
    {
        try {
            $movie = $this->movieRepository->findByTmdbId($tmdbId);

            if (!$movie) {
                throw new Exception('Filme não encontrado.');
            }

            if (!$this->favoriteMovieRepository->isFavorite($user, $movie)) {
                throw new Exception('Filme não está nos seus favoritos.');
            }

            $this->favoriteMovieRepository->removeFavorite($user, $movie);

            return $this->formatMovieResponse($movie);

        } catch (Exception $e) {
            throw new Exception('Não foi possível remover o filme. Erro: ' . $e->getMessage());
        }
    }

    /**
     * Formata os dados do filme para retorno em API ou respostas.
     *
     * @param Movie $movie Objeto do filme.
     * @return array Dados formatados do filme.
     */
    private function formatMovieResponse(Movie $movie): array
    {
        return [
            'id'           => $movie->id,
            'tmdb_id'      => $movie->tmdb_id,
            'title'        => $movie->title,
            'overview'     => $movie->overview,
            'poster_path'  => $movie->poster_path,
            'release_date' => !empty($movie->release_date) ? $movie->release_date : null,
            'genres'       => $movie->genres()->get(['id', 'name']),
        ];
    }
}
