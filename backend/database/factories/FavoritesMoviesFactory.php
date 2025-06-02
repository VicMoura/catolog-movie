<?php

namespace Database\Factories;

use App\Models\FavoriteMovie;
use App\Models\User;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

class FavoritesMoviesFactory extends Factory
{
    protected $model = FavoriteMovie::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'movie_id' => Movie::factory(),
        ];
    }
}
