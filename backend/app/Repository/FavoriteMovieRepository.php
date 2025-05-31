<?php

namespace App\Repositories;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Support\Collection;

class FavoriteMovieRepository
{
    /**
     * Adiciona um filme à lista de favoritos do usuário.
     *
     * @param User  $user  Usuário que deseja favoritar o filme.
     * @param Movie $movie Filme a ser adicionado aos favoritos.
     * @return void
     */
    public function addFavorite(User $user, Movie $movie): void
    {
        // Usa syncWithoutDetaching para não remover outros favoritos já existentes
        $user->favoriteMovies()->syncWithoutDetaching($movie->id);
    }

    /**
     * Remove um filme da lista de favoritos do usuário.
     *
     * @param User  $user  Usuário que deseja remover o filme.
     * @param Movie $movie Filme a ser removido dos favoritos.
     * @return void
     */
    public function removeFavorite(User $user, Movie $movie): void
    {
        // Remove o relacionamento do filme favorito do usuário
        $user->favoriteMovies()->detach($movie->id);
    }

    /**
     * Verifica se um filme está nos favoritos do usuário.
     *
     * @param User  $user  Usuário para checagem.
     * @param Movie $movie Filme a ser verificado.
     * @return bool True se estiver favoritado, False caso contrário.
     */
    public function isFavorite(User $user, Movie $movie): bool
    {
        // Consulta a existência do filme favorito para o usuário
        return $user->favoriteMovies()
            ->where('movie_id', $movie->id)
            ->exists();
    }

    /**
     * Retorna uma coleção dos filmes favoritos do usuário, 
     * podendo ser filtrada por gênero.
     *
     * @param User     $user    Usuário cujos favoritos serão listados.
     * @param int|null $genreId (Opcional) ID do gênero para filtro.
     * @return Collection Lista de filmes favoritos.
     */
    public function listFavorites(User $user, ?int $genreId = null): Collection
    {
        $query = $user->favoriteMovies()->with('genres');

        if ($genreId) {
            $query->whereHas('genres', function ($q) use ($genreId) {
                $q->where('genres.id', $genreId);
            });
        }

        return $query->get();
    }
}
