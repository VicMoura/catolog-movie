<?php

namespace Tests\Unit;

use App\Models\Movie;
use App\Repository\MovieRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MovieRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected MovieRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new MovieRepository();
    }

    /** @test */
    public function it_creates_a_movie_when_it_does_not_exist()
    {
        $data = [
            'id' => 1234,
            'title' => 'Test Movie',
            'overview' => 'This is a test movie.',
            'poster_path' => '/test.jpg',
            'release_date' => '2025-01-01',
        ];

        $movie = $this->repository->storeOrUpdate($data);

        $this->assertDatabaseHas('movies', [
            'tmdb_id' => 1234,
            'title' => 'Test Movie',
            'overview' => 'This is a test movie.',
            'poster_path' => '/test.jpg',
            'release_date' => '2025-01-01',
        ]);

        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertEquals(1234, $movie->tmdb_id);
    }

    /** @test */
    public function it_updates_a_movie_when_it_already_exists()
    {
        // Cria um filme existente
        $movie = Movie::factory()->create([
            'tmdb_id' => 1234,
            'title' => 'Old Title',
            'overview' => 'Old overview',
            'poster_path' => '/old.jpg',
            'release_date' => '2020-01-01',
        ]);

        // Dados atualizados
        $data = [
            'id' => 1234,
            'title' => 'Updated Movie',
            'overview' => 'This is an updated overview.',
            'poster_path' => '/updated.jpg',
            'release_date' => '2025-06-01',
        ];

        $updatedMovie = $this->repository->storeOrUpdate($data);

        $this->assertEquals($movie->id, $updatedMovie->id); // Mesmo registro
        $this->assertEquals('Updated Movie', $updatedMovie->title);
        $this->assertEquals('This is an updated overview.', $updatedMovie->overview);

        $this->assertDatabaseHas('movies', [
            'tmdb_id' => 1234,
            'title' => 'Updated Movie',
            'overview' => 'This is an updated overview.',
            'poster_path' => '/updated.jpg',
            'release_date' => '2025-06-01',
        ]);
    }

    /** @test */
    public function it_finds_a_movie_by_tmdb_id()
    {
        $movie = Movie::factory()->create([
            'tmdb_id' => 5678,
        ]);

        $foundMovie = $this->repository->findByTmdbId(5678);

        $this->assertInstanceOf(Movie::class, $foundMovie);
        $this->assertEquals($movie->id, $foundMovie->id);
    }

    /** @test */
    public function it_returns_null_when_movie_is_not_found()
    {
        $foundMovie = $this->repository->findByTmdbId(9999);

        $this->assertNull($foundMovie);
    }
}
