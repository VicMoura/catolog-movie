<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Class UserService
 *
 * Serviço responsável pela gestão de autenticação e cadastro de usuários.
 */
class UserService
{
    /**
     * Registra um novo usuário e gera um token de acesso.
     *
     * @param array $data Dados validados contendo name, email e password.
     * @return array Dados do usuário e token de autenticação.
     */
    public function create(array $data): array
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'          => $user,
            'access_token'  => $token,
            'token_type'    => 'Bearer',
        ];
    }

    /**
     * Autentica um usuário e gera um token de acesso.
     *
     * @param array $data Dados de login contendo email e password.
     * @return array Dados do usuário e token de autenticação.
     *
     * @throws ValidationException Se as credenciais forem inválidas.
     */
    public function login(array $data): array
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'          => $user,
            'access_token'  => $token,
            'token_type'    => 'Bearer',
        ];
    }

    /**
     * Realiza logout do usuário autenticado, revogando o token atual.
     *
     * @param User $user Usuário autenticado.
     * @return void
     */
    public function logout(User $user): void
    {
        $token = $user->currentAccessToken();

        if ($token) {
            $token->delete();
        }
    }
}
