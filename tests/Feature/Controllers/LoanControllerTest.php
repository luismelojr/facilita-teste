<?php

namespace Tests\Feature\Controllers;

use App\Enums\BookStatusEnum;
use App\Enums\LoanStatusEnum;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoanControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;


    public function test_it_can_get_all_loans()
    {
        // Arrange
        Loan::factory()->count(3)->create();

        // Act
        $response = $this->getJson('/api/v1/loans');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'message',
                'success'
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Empréstimos obtidos com sucesso'
            ]);

        $this->assertCount(3, $response->json('data'));
    }


    public function test_it_can_get_loan_by_id()
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $loan = Loan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'due_date' => Carbon::now()->addDays(7),
            'status' => LoanStatusEnum::ACTIVE->value
        ]);

        // Act
        $response = $this->getJson("/api/v1/loans/{$loan->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'user_id',
                    'book_id',
                    'due_date',
                    'status',
                    'created_at',
                    'updated_at'
                ],
                'message',
                'success'
            ])
            ->assertJson([
                'data' => [
                    'id' => $loan->id,
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                    'status' => LoanStatusEnum::ACTIVE->value
                ],
                'success' => true,
                'message' => 'Empréstimo obtido com sucesso'
            ]);
    }


    public function test_it_returns_404_when_loan_not_found()
    {
        // Act
        $response = $this->getJson("/api/v1/loans/999");

        // Assert
        $response->assertStatus(404);
    }


    public function test_it_can_create_a_loan()
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->available()->create();

        $loanData = [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'due_date' => Carbon::now()->addDays(14)->format('Y-m-d')
        ];

        // Act
        $response = $this->postJson('/api/v1/loans', $loanData);

        // Assert
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'user_id',
                    'book_id',
                    'due_date',
                    'status',
                    'created_at',
                    'updated_at'
                ],
                'message',
                'success'
            ])
            ->assertJson([
                'data' => [
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                    'status' => LoanStatusEnum::ACTIVE->value
                ],
                'success' => true,
                'message' => 'Empréstimo criado com sucesso'
            ]);

        $this->assertDatabaseHas('loans', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => LoanStatusEnum::ACTIVE->value
        ]);

        // Verificar se o status do livro foi atualizado para emprestado
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'status' => BookStatusEnum::BORROWED->value
        ]);
    }


    public function test_it_validates_required_fields_when_creating_loan()
    {
        // Arrange
        $loanData = [
            'user_id' => '',
            'book_id' => ''
        ];

        // Act
        $response = $this->postJson('/api/v1/loans', $loanData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['user_id', 'book_id']);
    }


    public function test_it_cannot_create_loan_for_unavailable_book()
    {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->borrowed()->create();

        $loanData = [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'due_date' => Carbon::now()->addDays(14)->format('Y-m-d')
        ];

        // Act
        $response = $this->postJson('/api/v1/loans', $loanData);

        // Assert
        $response->assertStatus(409) // Conflict status
        ->assertJson([
            'success' => false,
            'message' => 'O livro não está disponível para empréstimo.'  // Adicionado o ponto final
        ]);
    }

    public function test_it_can_return_a_book()
    {
        // Arrange
        $book = Book::factory()->borrowed()->create();
        $loan = Loan::factory()->create([
            'book_id' => $book->id,
            'status' => LoanStatusEnum::ACTIVE->value
        ]);

        // Act
        $response = $this->postJson("/api/v1/loans/{$loan->id}/return");

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Livro devolvido com sucesso'
            ]);

        // Verificar se o status do empréstimo foi atualizado para devolvido
        $this->assertDatabaseHas('loans', [
            'id' => $loan->id,
            'status' => LoanStatusEnum::RETURNED->value
        ]);

        // Verificar se o status do livro foi atualizado para disponível
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'status' => BookStatusEnum::AVAILABLE->value
        ]);
    }
}
