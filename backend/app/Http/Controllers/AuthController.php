<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\RegisterUserRequest;
use App\Http\Requests\User\LoginUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    //
    public function register(RegisterUserRequest $request)
    {
        
        $user = User::register([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 'Usuário criado com sucesso', 201);

    }

    public function login(LoginUserRequest $request)
    {

        $user = User::login(['email' => $request->email, 'password' => $request->password]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'user'          => $user,
            'access_token'  => $token,
            'token_type'    => 'Bearer'
        ], 'Logado com sucesso.');
    }

    public function me(Request  $request)
    {
        return $this->success([
            'user' => $request->user()
        ], 'Dados do usuário atual.');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Deslogado com sucesso!');
    }
}
