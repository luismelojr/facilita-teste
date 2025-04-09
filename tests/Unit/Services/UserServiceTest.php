<?php

namespace Tests\Unit\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    protected $userRepository;
    protected $userService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->userService = new UserService($this->userRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_users(): void
    {

        $users = new Collection([
            new User(['id' => 1, 'name' => 'User 1']),
            new User(['id' => 2, 'name' => 'User 2'])
        ]);

        $this->userRepository->shouldReceive('getAll')
            ->once()
            ->andReturn($users);


        $result = $this->userService->getAllUsers();


        $this->assertCount(2, $result);
    }

    public function test_get_user_by_id(): void
    {

        $user = new User(['id' => 1, 'name' => 'John Doe']);
        $userId = 1;

        $this->userRepository->shouldReceive('getById')
            ->once()
            ->with($userId)
            ->andReturn($user);


        $result = $this->userService->getUserById($userId);


        $this->assertSame($user, $result);
    }

    public function test_create_user(): void
    {

        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'registration_number' => 'REG123',
            'password' => 'password123'
        ];

        $createdUser = new User($userData);

        $this->userRepository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($argument) use ($userData) {

                return
                    $argument['name'] === $userData['name'] &&
                    $argument['email'] === $userData['email'] &&
                    $argument['registration_number'] === $userData['registration_number'] &&
                    Hash::check($userData['password'], $argument['password']);
            }))
            ->andReturn($createdUser);


        $result = $this->userService->createUser($userData);


        $this->assertSame($createdUser, $result);
    }

    public function test_update_user(): void
    {

        $userId = 1;
        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ];

        $this->userRepository->shouldReceive('update')
            ->once()
            ->with($userId, Mockery::on(function ($argument) use ($updateData) {

                return
                    $argument['name'] === $updateData['name'] &&
                    $argument['email'] === $updateData['email'];
            }))
            ->andReturn(true);


        $result = $this->userService->updateUser($userId, $updateData);


        $this->assertTrue($result);
    }

    public function test_update_user_with_password(): void
    {

        $userId = 1;
        $updateData = [
            'name' => 'Updated Name',
            'password' => 'newpassword123'
        ];

        $this->userRepository->shouldReceive('update')
            ->once()
            ->with($userId, Mockery::on(function ($argument) use ($updateData) {
                return
                    $argument['name'] === $updateData['name'] &&
                    Hash::check($updateData['password'], $argument['password']);
            }))
            ->andReturn(true);


        $result = $this->userService->updateUser($userId, $updateData);


        $this->assertTrue($result);
    }

    public function test_delete_user(): void
    {

        $userId = 1;

        $this->userRepository->shouldReceive('delete')
            ->once()
            ->with($userId)
            ->andReturn(true);


        $result = $this->userService->deleteUser($userId);


        $this->assertTrue($result);
    }

    public function test_is_email_taken_when_not_used(): void
    {

        $email = 'test@example.com';

        $this->userRepository->shouldReceive('findByEmail')
            ->once()
            ->with($email)
            ->andReturn(null);


        $result = $this->userService->isEmailTaken($email);


        $this->assertFalse($result);
    }

    public function test_is_email_taken_when_used(): void
    {

        $email = 'test@example.com';
        $existingUser = new User(['id' => 1, 'email' => $email]);

        $this->userRepository->shouldReceive('findByEmail')
            ->once()
            ->with($email)
            ->andReturn($existingUser);


        $result = $this->userService->isEmailTaken($email);


        $this->assertTrue($result);
    }

    public function test_is_email_taken_with_exception_when_same_user(): void
    {

        $email = 'test@example.com';
        $userId = 1;
        $existingUser = Mockery::mock(User::class);
        $existingUser->shouldReceive('getAttribute')->with('id')->andReturn($userId);
        $existingUser->shouldReceive('getAttribute')->with('email')->andReturn($email);

        $this->userRepository->shouldReceive('findByEmail')
            ->once()
            ->with($email)
            ->andReturn($existingUser);


        $result = $this->userService->isEmailTaken($email, $userId);


        $this->assertFalse($result, "Should return false when checking a user's own email");
    }

    public function test_is_registration_number_taken_when_not_used(): void
    {

        $registrationNumber = 'REG123';

        $this->userRepository->shouldReceive('findByRegistrationNumber')
            ->once()
            ->with($registrationNumber)
            ->andReturn(null);


        $result = $this->userService->isRegistrationNumberTaken($registrationNumber);


        $this->assertFalse($result);
    }

    public function test_is_registration_number_taken_when_used(): void
    {

        $registrationNumber = 'REG123';
        $existingUser = new User(['id' => 1, 'registration_number' => $registrationNumber]);

        $this->userRepository->shouldReceive('findByRegistrationNumber')
            ->once()
            ->with($registrationNumber)
            ->andReturn($existingUser);


        $result = $this->userService->isRegistrationNumberTaken($registrationNumber);


        $this->assertTrue($result);
    }

    public function test_is_registration_number_taken_with_exception_when_same_user(): void
    {

        $registrationNumber = 'REG123';
        $userId = 1;

        $existingUser = Mockery::mock(User::class);
        $existingUser->shouldReceive('getAttribute')->with('id')->andReturn($userId);
        $existingUser->shouldReceive('getAttribute')->with('registration_number')->andReturn($registrationNumber);

        $this->userRepository->shouldReceive('findByRegistrationNumber')
            ->once()
            ->with($registrationNumber)
            ->andReturn($existingUser);


        $result = $this->userService->isRegistrationNumberTaken($registrationNumber, $userId);


        $this->assertFalse($result, "Should return false when checking a user's own registration number");
    }

    public function test_get_user_loans(): void
    {

        $userId = 1;
        $userLoans = new Collection([
            Mockery::mock('Loan'),
            Mockery::mock('Loan')
        ]);

        $this->userRepository->shouldReceive('getUserLoans')
            ->once()
            ->with($userId)
            ->andReturn($userLoans);


        $result = $this->userService->getUserLoans($userId);


        $this->assertCount(2, $result);
        $this->assertInstanceOf(Collection::class, $result);
    }
}
