<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UserService
{

    public function __construct(
        protected UserRepositoryInterface $userRepository
    ){}

    /**
     * Obter todos os usuários
     *
     * @return Collection
     */
    public function getAllUsers(): Collection
    {
        return $this->userRepository->getAll();
    }

    /**
     * Obter usuário por ID
     *
     * @param int $userId
     * @return Model
     */
    public function getUserById(int $userId): Model
    {
        return $this->userRepository->getById($userId);
    }

    /**
     * Criar novo usuário
     *
     * @param array $userData
     * @return User
     */
    public function createUser(array $userData): Model
    {
        if (isset($userData['password'])) {
            $userData['password'] = Hash::make($userData['password']);
        }

        return $this->userRepository->create($userData);
    }

    /**
     * Atualizar usuário existente
     *
     * @param int $userId
     * @param array $userData
     * @return bool
     */
    public function updateUser(int $userId, array $userData): bool
    {
        if (isset($userData['password'])) {
            $userData['password'] = Hash::make($userData['password']);
        }

        return $this->userRepository->update($userId, $userData);
    }

    /**
     * Excluir usuário
     *
     * @param int $userId
     * @return bool
     */
    public function deleteUser(int $userId): bool
    {
        return $this->userRepository->delete($userId);
    }

    /**
     * Verificar se email já está em uso
     *
     * @param string $email
     * @param int|null $exceptUserId
     * @return bool
     */
    public function isEmailTaken(string $email, ?int $exceptUserId = null): bool
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return false;
        }

        if ($exceptUserId && $user->id === $exceptUserId) {
            return false;
        }

        return true;
    }

    /**
     * Verificar se número de registro já está em uso
     *
     * @param string $registrationNumber
     * @param int|null $exceptUserId
     * @return bool
     */
    public function isRegistrationNumberTaken(string $registrationNumber, ?int $exceptUserId = null): bool
    {
        $user = $this->userRepository->findByRegistrationNumber($registrationNumber);

        if (!$user) {
            return false;
        }

        if ($exceptUserId && $user->id === $exceptUserId) {
            return false;
        }

        return true;
    }

    /**
     * Obter empréstimos do usuário
     *
     * @param int $userId
     * @return Collection
     */
    public function getUserLoans(int $userId): Collection
    {
        return $this->userRepository->getUserLoans($userId);
    }
}
