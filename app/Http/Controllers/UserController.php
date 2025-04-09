<?php

namespace App\Http\Controllers;

use App\DTO\UserDTO;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * @var UserService
     */
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
     * Listar todos os usuários
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $users = $this->userService->getAllUsers();

        return response()->json([
            'data' => $users,
            'message' => 'Usuários obtidos com sucesso',
            'success' => true
        ]);
    }

    /**
     * Obter usuário específico
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        return response()->json([
            'data' => $user,
            'message' => 'Usuário obtido com sucesso',
            'success' => true
        ]);
    }

    /**
     * Criar novo usuário
     *
     * @param UserStoreRequest $request
     * @return JsonResponse
     */
    public function store(UserStoreRequest $request): JsonResponse
    {
        $userDTO = UserDTO::fromArray($request->validated());
        $user = $this->userService->createUser($userDTO->toArray());

        return response()->json([
            'data' => $user,
            'message' => 'Usuário criado com sucesso',
            'success' => true
        ], Response::HTTP_CREATED);
    }

    /**
     * Atualizar usuário existente
     *
     * @param UserUpdateRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UserUpdateRequest $request, int $id): JsonResponse
    {
        $userData = $request->validated();
        $this->userService->updateUser($id, $userData);

        $user = $this->userService->getUserById($id);

        return response()->json([
            'data' => $user,
            'message' => 'Usuário atualizado com sucesso',
            'success' => true
        ]);
    }

    /**
     * Remover usuário
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $this->userService->deleteUser($id);

        return response()->json([
            'message' => 'Usuário removido com sucesso',
            'success' => true
        ]);
    }

    /**
     * Obter empréstimos do usuário
     *
     * @param int $id
     * @return JsonResponse
     */
    public function loans(int $id): JsonResponse
    {
        $loans = $this->userService->getUserLoans($id);

        return response()->json([
            'data' => $loans,
            'message' => 'Empréstimos do usuário obtidos com sucesso',
            'success' => true
        ]);
    }
}
