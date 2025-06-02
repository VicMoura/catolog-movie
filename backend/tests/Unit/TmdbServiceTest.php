<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\TmdbService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class TmdbServiceTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        // Mock da configuração
        Config::set('services.tmdb.base_url', 'https://api.themoviedb.org/3');
        Config::set('services.tmdb.api_key', 'fake_api_key');
    }

    public function test_search_movies_returns_results()
    {
        // Simula resposta da API
        Http::fake([
            'https://api.themoviedb.org/3/search/movie*' => Http::response([
                'results' => [
                    ['id' => 1, 'title' => 'Filme Teste'],
                ]
            ], 200),
        ]);

        // Garante que o cache está limpo
        Cache::flush();

        $tmdbService = new TmdbService();

        $result = $tmdbService->searchMovies('Filme Teste');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('results', $result);
        $this->assertEquals('Filme Teste', $result['results'][0]['title']);
    }

    public function test_get_movie_details_returns_data()
    {
        Http::fake([
            'https://api.themoviedb.org/3/movie/123*' => Http::response([
                'id' => 123,
                'title' => 'Filme Detalhe',
            ], 200),
        ]);

        Cache::flush();

        $tmdbService = new TmdbService();

        $result = $tmdbService->getMovieDetails(123);

        $this->assertIsArray($result);
        $this->assertEquals(123, $result['id']);
        $this->assertEquals('Filme Detalhe', $result['title']);
    }

    public function test_get_movie_videos_returns_results()
    {
        Http::fake([
            'https://api.themoviedb.org/3/movie/123/videos*' => Http::response([
                'results' => [
                    ['id' => 'abc', 'name' => 'Trailer'],
                ],
            ], 200),
        ]);

        Cache::flush();

        $tmdbService = new TmdbService();

        $result = $tmdbService->getMovieVideos(123);

        $this->assertIsArray($result);
        $this->assertEquals('Trailer', $result[0]['name']);
    }

    public function test_get_movie_credits_returns_data()
    {
        Http::fake([
            'https://api.themoviedb.org/3/movie/123/credits*' => Http::response([
                'cast' => [
                    ['id' => 1, 'name' => 'Ator Teste'],
                ],
                'crew' => [
                    ['id' => 2, 'name' => 'Diretor Teste'],
                ],
            ], 200),
        ]);

        Cache::flush();

        $tmdbService = new TmdbService();

        $result = $tmdbService->getMovieCredits(123);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('cast', $result);
        $this->assertEquals('Ator Teste', $result['cast'][0]['name']);
    }
}
