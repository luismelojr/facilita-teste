<?php

namespace Tests\Unit\Repositories;

use App\Models\User;
use App\Models\Loan;
use App\Models\Book;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository(new User());
    }


    public function test_it_can_get_all_users()
    {

        User::factory()->count(3)->create();


        $result = $this->userRepository->getAll();


        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
    }


    public function test_it_can_get_user_by_id()
    {

        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'registration_number' => 'REG12345'
        ]);


        $foundUser = $this->userRepository->getById($user->id);


        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals($user->id, $foundUser->id);
        $this->assertEquals('John Doe', $foundUser->name);
        $this->assertEquals('john@example.com', $foundUser->email);
        $this->assertEquals('REG12345', $foundUser->registration_number);
    }


    public function test_it_can_create_a_user()
    {

        $userData = [
            'name' => 'José Silva',
            'email' => 'jose@example.com',
            'registration_number' => 'REG67890',
            'password' => bcrypt('password123')
        ];


        $user = $this->userRepository->create($userData);


        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', [
            'name' => 'José Silva',
            'email' => 'jose@example.com',
            'registration_number' => 'REG67890'
        ]);
    }


    public function test_it_can_update_a_user()
    {

        $user = User::factory()->create([
            'name' => 'Maria Santos',
            'email' => 'maria@example.com',
            'registration_number' => 'REG54321'
        ]);

        $updateData = [
            'name' => 'Maria Silva Santos',
            'email' => 'maria.silva@example.com'
        ];


        $result = $this->userRepository->update($user->id, $updateData);


        $this->assertTrue($result);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Maria Silva Santos',
            'email' => 'maria.silva@example.com',
            'registration_number' => 'REG54321'
        ]);
    }


    public function test_it_can_delete_a_user()
    {

        $user = User::factory()->create();


        $result = $this->userRepository->delete($user->id);


        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }


    public function test_it_can_find_user_by_email()
    {

        $user = User::factory()->create([
            'email' => 'ana@example.com'
        ]);

        User::factory()->count(2)->create();


        $foundUser = $this->userRepository->findByEmail('ana@example.com');


        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals($user->id, $foundUser->id);
        $this->assertEquals('ana@example.com', $foundUser->email);
    }


    public function test_it_returns_null_when_email_not_found()
    {

        User::factory()->create([
            'email' => 'carlos@example.com'
        ]);


        $result = $this->userRepository->findByEmail('naoexiste@example.com');


        $this->assertNull($result);
    }


    public function test_it_can_find_user_by_registration_number()
    {

        $user = User::factory()->create([
            'registration_number' => 'REG99999'
        ]);

        User::factory()->count(2)->create();


        $foundUser = $this->userRepository->findByRegistrationNumber('REG99999');


        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals($user->id, $foundUser->id);
        $this->assertEquals('REG99999', $foundUser->registration_number);
    }


    public function test_it_returns_null_when_registration_number_not_found()
    {

        User::factory()->create([
            'registration_number' => 'REG12345'
        ]);


        $result = $this->userRepository->findByRegistrationNumber('NAOEXISTE');


        $this->assertNull($result);
    }


    public function test_it_can_get_user_loans()
    {

        $user = User::factory()->create();


        $book1 = Book::factory()->create();
        $book2 = Book::factory()->create();

        Loan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book1->id
        ]);

        Loan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book2->id
        ]);


        $userLoans = $this->userRepository->getUserLoans($user->id);


        $this->assertInstanceOf(Collection::class, $userLoans);
        $this->assertCount(2, $userLoans);
        foreach ($userLoans as $loan) {
            $this->assertEquals($user->id, $loan->user_id);
        }
    }


    public function test_it_can_find_by_criteria()
    {

        User::factory()->create([
            'name' => 'Roberto Ferreira',
            'email' => 'roberto@example.com'
        ]);

        User::factory()->create([
            'name' => 'Roberto Silva',
            'email' => 'roberto.silva@example.com'
        ]);

        User::factory()->create([
            'name' => 'Ana Ferreira',
            'email' => 'ana@example.com'
        ]);


        $criteria = [
            ['name', 'like', '%Ferreira%']
        ];

        $users = $this->userRepository->findByCriteria($criteria);


        $this->assertInstanceOf(Collection::class, $users);
        $this->assertCount(2, $users);


        foreach ($users as $user) {
            $this->assertStringContainsString('Ferreira', $user->name);
        }
    }
}
