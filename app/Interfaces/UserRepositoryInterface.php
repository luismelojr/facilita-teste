<?php

namespace App\Interfaces;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     * Encontrar usuário pelo email
     *
     * @param string $email Email do usuário
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * Encontrar usuário pelo número de registro
     *
     * @param string $registrationNumber Número de registro do usuário
     * @return User|null
     */
    public function findByRegistrationNumber(string $registrationNumber): ?User;

    /**
     * Obter todos os empréstimos de um usuário
     *
     * @param int $userId ID do usuário
     * @return Collection
     */
    public function getUserLoans(int $userId): Collection;
}
