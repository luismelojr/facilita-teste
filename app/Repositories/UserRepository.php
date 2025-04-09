<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * UserRepository constructor.
     *
     * @param User $model
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * @inheritdoc
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * @inheritdoc
     */
    public function findByRegistrationNumber(string $registrationNumber): ?User
    {
        return $this->model->where('registration_number', $registrationNumber)->first();
    }

    /**
     * @inheritdoc
     */
    public function getUserLoans(int $userId): Collection
    {
        $user = $this->getById($userId);
        return $user->loans()->with('book')->get();
    }
}
