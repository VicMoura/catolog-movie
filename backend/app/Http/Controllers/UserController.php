<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\RegisterUserRequest;
use App\Http\Requests\User\LoginUserRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Class UserController
 *
 * Controlador responsável pela autenticação e gestão do usuário autenticado.
 */
class UserController extends Controller
{
    protected UserService $userService;

    /**
     * UserController constructor.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Registra um novo usuário.
     *
     * @param RegisterUserRequest $request
     * @return JsonResponse
     */
    public function create(RegisterUserRequest $request): JsonResponse
    {
        $data = $this->userService->create($request->validated());

        return $this->success($data, 'Usuário registrado com sucesso.', 201);
    }

    /**
     * Realiza o login do usuário.
     *
     * @param LoginUserRequest $request
     * @return JsonResponse
     */
    public function login(LoginUserRequest $request): JsonResponse
    {
        $data = $this->userService->login($request->validated());

        return $this->success($data, 'Login realizado com sucesso.');
    }

    /**
     * Retorna os dados do usuário autenticado.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function detail(Request $request): JsonResponse
    {
        return $this->success([
            'user' => $request->user(),
        ], 'Dados do usuário atual.');
    }

    /**
     * Realiza o logout do usuário (revoga o token atual).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $this->userService->logout($request->user());

        return $this->success(null, 'Logout realizado com sucesso.');
    }
}