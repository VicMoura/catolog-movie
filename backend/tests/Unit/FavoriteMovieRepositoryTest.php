<?php

namespace Tests\Unit;

use App\Models\Movie;
use App\Models\User;
use App\Repository\FavoriteMovieRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteMovieRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected FavoriteMovieRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new FavoriteMovieRepository();
    }

    /** @test */
    public function it_can_add_a_movie_to_favorites()
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create();

        $this->repository->addFavorite($user, $movie);

        $this->assertTrue(
            $user->favoriteMovies()->where('movie_id', $movie->id)->exists()
        );
    }

    /** @test */
    public function it_can_remove_a_movie_from_favorites()
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create();

        $user->favoriteMovies()->attach($movie->id);

        $this->repository->removeFavorite($user, $movie);

        $this->assertFalse(
            $user->favoriteMovies()->where('movie_id', $movie->id)->exists()
        );
    }

    /** @test */
    public function it_can_check_if_a_movie_is_favorite()
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create();

        $this->assertFalse($this->repository->isFavorite($user, $movie));

        $user->favoriteMovies()->attach($movie->id);

        $this->assertTrue($this->repository->isFavorite($user, $movie));
    }

    /** @test */
    public function it_can_list_all_favorites()
    {
        $user = User::factory()->create();
        $movies = Movie::factory()->count(3)->create();

        $user->favoriteMovies()->attach($movies->pluck('id'));

        $favorites = $this->repository->listFavorites($user);

        $this->assertCount(3, $favorites);
        $this->assertEqualsCanonicalizing(
            $movies->pluck('id')->toArray(),
            $favorites->pluck('id')->toArray()
        );
    }

    /** @test */
    public function it_can_list_favorites_filtered_by_genre()
    {
        $user = User::factory()->create();
        $genre = \App\Models\Genre::factory()->create();

        $movieWithGenre = Movie::factory()->create();
        $movieWithoutGenre = Movie::factory()->create();

        $movieWithGenre->genres()->attach($genre->id);

        $user->favoriteMovies()->attach([
            $movieWithGenre->id,
            $movieWithoutGenre->id,
        ]);

        $favorites = $this->repository->listFavorites($user, $genre->id);

        $this->assertCount(1, $favorites);
        $this->assertTrue($favorites->first()->is($movieWithGenre));
    }
}
