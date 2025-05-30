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

        return response()->json([
            'message' => 'Usuário criado com sucesso',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(LoginUserRequest $request)
    {

        $user = User::login(['email' => $request->email, 'password' => $request->password]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Logado com sucesso.',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function me(Request  $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Deslogado com sucesso!'
        ]);
    }
}
