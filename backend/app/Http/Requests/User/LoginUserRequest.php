<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class LoginUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => 'O email é obrigatório.',
            'email.email'       => 'Informe um email válido.',
            'email.exists'      => 'Este email não está cadastrado.',
            'password.required' => 'A senha é obrigatória.',
            'password.string'   => 'A senha deve ser um texto.',
        ];
    }
}
