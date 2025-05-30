<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFavoriteMovieRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'movie_id' => 'required|exists:movies,id',
        ];
    }


    public function messages()
    {
        return [
            'movie_id.required' => 'O campo movie_id é obrigatório.',
            'movie_id.exists' => 'O filme informado não existe.',
        ];
    }
}
