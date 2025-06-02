<?php

namespace App\Http\Requests\FavoritesMovies;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Movie;
use App\Models\FavoriteMovie;


class CreateFavoritesMoviesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tmdb_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $movie = Movie::where('tmdb_id', $value)->first();

                    if ($movie) {
                        $exists = FavoriteMovie::where('user_id', $this->user()->id)
                            ->where('movie_id', $movie->id)
                            ->exists();

                        if ($exists) {
                            $fail('Esse filme já está nos seus favoritos.');
                        }
                    }
                }
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'tmdb_id.required' => 'O ID do filme (TMDB) é obrigatório.',
            'tmdb_id.integer'  => 'O ID do filme (TMDB) deve ser um número inteiro.',
        ];
    }
}
