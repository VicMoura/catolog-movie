<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovieRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tmdb_id'      => 'required|integer|unique:movies,tmdb_id',
            'title'        => 'required|string|max:255',
            'overview'     => 'nullable|string',
            'poster_path'  => 'nullable|string',
            'release_date' => 'nullable|date',
            'genres'       => 'nullable|array',
            'genres.*'     => 'integer|exists:genres,id',
        ];
    }

    public function messages(): array
    {
        return [
            'tmdb_id.required'     => 'O campo TMDB ID é obrigatório.',
            'tmdb_id.integer'      => 'O TMDB ID deve ser um número inteiro.',
            'tmdb_id.unique'       => 'Este filme já foi cadastrado.',

            'title.required'       => 'O título do filme é obrigatório.',
            'title.string'         => 'O título deve ser uma string.',
            'title.max'            => 'O título pode ter no máximo 255 caracteres.',

            'overview.string'      => 'A sinopse deve ser um texto válido.',

            'poster_path.string'   => 'O caminho do pôster deve ser uma string.',

            'release_date.date'    => 'A data de lançamento deve ser uma data válida.',

            'genres.array'         => 'Os gêneros devem ser um array.',
            'genres.*.integer'     => 'Cada gênero deve ser um ID numérico.',
            'genres.*.exists'      => 'Um dos gêneros selecionados é inválido.',
        ];
    }
}
