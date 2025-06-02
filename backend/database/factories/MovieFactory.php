<?php

namespace Database\Factories;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

class MovieFactory extends Factory
{
    protected $model = Movie::class;

    public function definition()
    {
        return [
            'tmdb_id' => $this->faker->unique()->numberBetween(1000, 999999),
            'title' => $this->faker->sentence(3),
            'overview' => $this->faker->paragraph(),
            'poster_path' => $this->faker->imageUrl(300, 450, 'movies'),
            'release_date' => $this->faker->date(),
        ];
    }
}
