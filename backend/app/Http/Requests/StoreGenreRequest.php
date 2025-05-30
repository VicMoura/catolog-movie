<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGenreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100|unique:genres,name',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do gênero é obrigatório.',
            'name.string'   => 'O nome do gênero deve ser um texto.',
            'name.max'      => 'O nome do gênero pode ter no máximo 100 caracteres.',
            'name.unique'   => 'Este gênero já está cadastrado.',
        ];
    }
}
