<?php

namespace App\Http\Requests\Favorite;

use Illuminate\Foundation\Http\FormRequest;

class CreateFavoritesMoviesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tmdb_id' => 'required|integer',
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
