<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Book;
use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;


    public function test_it_can_get_all_users()
    {
        // Arrange
        User::factory()->count(3)->create();

        // Act
        $response = $this->getJson('/api/v1/users');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'message',
                'success'
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Usuários obtidos com sucesso'
            ]);

        $this->assertCount(3, $response->json('data'));
    }


    public function test_it_can_get_user_by_id()
    {
        // Arrange
        $user = User::factory()->create([
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'registration_number' => 'REG12345'
        ]);

        // Act
        $response = $this->getJson("/api/v1/users/{$user->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'registration_number',
                    'created_at',
                    'updated_at'
                ],
                'message',
                'success'
            ])
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'name' => 'João Silva',
                    'email' => 'joao@example.com',
                    'registration_number' => 'REG12345'
                ],
                'success' => true,
                'message' => 'Usuário obtido com sucesso'
            ]);
    }


    public function test_it_returns_404_when_user_not_found()
    {
        // Act
        $response = $this->getJson("/api/v1/users/999");

        // Assert
        $response->assertStatus(404);
    }


    public function test_it_can_create_a_user()
    {
        // Arrange
        $userData = [
            'name' => 'Maria Santos',
            'email' => 'maria@example.com',
            'registration_number' => 'REG67890',
            'password' => 'senha123'
        ];

        // Act
        $response = $this->postJson('/api/v1/users', $userData);

        // Assert
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'registration_number',
                    'created_at',
                    'updated_at'
                ],
                'message',
                'success'
            ])
            ->assertJson([
                'data' => [
                    'name' => 'Maria Santos',
                    'email' => 'maria@example.com',
                    'registration_number' => 'REG67890'
                ],
                'success' => true,
                'message' => 'Usuário criado com sucesso'
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Maria Santos',
            'email' => 'maria@example.com',
            'registration_number' => 'REG67890'
        ]);
    }


    public function test_it_validates_required_fields_when_creating_user()
    {
        // Arrange
        $userData = [
            'name' => '',
            'email' => 'invalid-email',
            'registration_number' => '',
            'password' => 'short'
        ];

        // Act
        $response = $this->postJson('/api/v1/users', $userData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'registration_number', 'password']);
    }


    public function test_it_validates_unique_fields_when_creating_user()
    {
        // Arrange
        $existingUser = User::factory()->create([
            'email' => 'existing@example.com',
            'registration_number' => 'REG-EXISTING'
        ]);

        $userData = [
            'name' => 'Novo Usuário',
            'email' => 'existing@example.com',
            'registration_number' => 'REG-EXISTING',
            'password' => 'senha123'
        ];

        // Act
        $response = $this->postJson('/api/v1/users', $userData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'registration_number']);
    }


    public function test_it_can_update_a_user()
    {
        // Arrange
        $user = User::factory()->create([
            'name' => 'Pedro Oliveira',
            'email' => 'pedro@example.com',
            'registration_number' => 'REG54321'
        ]);

        $updateData = [
            'name' => 'Pedro Silva Oliveira',
            'email' => 'pedro.silva@example.com'
        ];

        // Act
        $response = $this->putJson("/api/v1/users/{$user->id}", $updateData);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'message',
                'success'
            ])
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'name' => 'Pedro Silva Oliveira',
                    'email' => 'pedro.silva@example.com',
                    'registration_number' => 'REG54321'
                ],
                'success' => true,
                'message' => 'Usuário atualizado com sucesso'
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Pedro Silva Oliveira',
            'email' => 'pedro.silva@example.com',
            'registration_number' => 'REG54321'
        ]);
    }


    public function test_it_validates_unique_fields_when_updating_user()
    {
        // Arrange
        $user1 = User::factory()->create([
            'email' => 'user1@example.com',
            'registration_number' => 'REG-USER1'
        ]);

        $user2 = User::factory()->create([
            'email' => 'user2@example.com',
            'registration_number' => 'REG-USER2'
        ]);

        $updateData = [
            'email' => 'user2@example.com',
            'registration_number' => 'REG-USER2'
        ];

        // Act
        $response = $this->putJson("/api/v1/users/{$user1->id}", $updateData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'registration_number']);
    }


    public function test_it_can_delete_a_user()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->deleteJson("/api/v1/users/{$user->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Usuário removido com sucesso'
            ]);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
