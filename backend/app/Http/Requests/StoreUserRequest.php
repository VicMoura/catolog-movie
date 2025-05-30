<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'O nome é obrigatório.',
            'name.string'       => 'O nome deve ser um texto.',
            'name.max'          => 'O nome pode ter no máximo 255 caracteres.',

            'email.required'    => 'O email é obrigatório.',
            'email.email'       => 'Informe um email válido.',
            'email.unique'      => 'Este email já está em uso.',

            'password.required' => 'A senha é obrigatória.',
            'password.string'   => 'A senha deve ser um texto.',
            'password.min'      => 'A senha deve ter no mínimo 6 caracteres.',
            'password.confirmed'=> 'A confirmação da senha não confere.',
        ];
    }
}
