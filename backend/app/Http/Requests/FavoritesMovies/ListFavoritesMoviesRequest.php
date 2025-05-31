<?php

namespace App\Http\Requests\Favorite;

use Illuminate\Foundation\Http\FormRequest;

class ListFavoritesMoviesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'genre_id' => 'nullable|integer|exists:genres,id',
        ];
    }

    public function messages(): array
    {
        return [
            'genre_id.integer' => 'O ID do gênero deve ser um número inteiro.',
            'genre_id.exists'  => 'O gênero informado não foi encontrado.',
        ];
    }
}
