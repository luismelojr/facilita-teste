<?php

namespace Tests\Feature\Controllers;

use App\Enums\BookGenreEnum;
use App\Enums\BookStatusEnum;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;


    public function test_it_can_get_all_books()
    {
        // Arrange
        Book::factory()->count(3)->create();

        // Act
        $response = $this->getJson('/api/v1/books');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'message',
                'success'
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Livros obtidos com sucesso'
            ]);

        $this->assertCount(3, $response->json('data'));
    }


    public function test_it_can_get_book_by_id()
    {
        // Arrange
        $book = Book::factory()->create([
            'title' => 'Dom Casmurro',
            'author' => 'Machado de Assis',
            'registration_number' => 'BOOK12345',
            'genre' => BookGenreEnum::FICTION->value,
            'status' => BookStatusEnum::AVAILABLE->value
        ]);

        // Act
        $response = $this->getJson("/api/v1/books/{$book->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'author',
                    'registration_number',
                    'genre',
                    'status',
                    'created_at',
                    'updated_at'
                ],
                'message',
                'success'
            ])
            ->assertJson([
                'data' => [
                    'id' => $book->id,
                    'title' => 'Dom Casmurro',
                    'author' => 'Machado de Assis',
                    'registration_number' => 'BOOK12345',
                    'genre' => BookGenreEnum::FICTION->value,
                    'status' => BookStatusEnum::AVAILABLE->value
                ],
                'success' => true,
                'message' => 'Livro obtido com sucesso'
            ]);
    }


    public function test_it_returns_404_when_book_not_found()
    {
        // Act
        $response = $this->getJson("/api/v1/books/999");

        // Assert
        $response->assertStatus(404);
    }


    public function test_it_can_create_a_book()
    {
        // Arrange
        $bookData = [
            'title' => 'O Senhor dos Anéis',
            'author' => 'J.R.R. Tolkien',
            'registration_number' => 'BOOK67890',
            'genre' => BookGenreEnum::FANTASY->value
        ];

        // Act
        $response = $this->postJson('/api/v1/books', $bookData);

        // Assert
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'author',
                    'registration_number',
                    'genre',
                    'status',
                    'created_at',
                    'updated_at'
                ],
                'message',
                'success'
            ])
            ->assertJson([
                'data' => [
                    'title' => 'O Senhor dos Anéis',
                    'author' => 'J.R.R. Tolkien',
                    'registration_number' => 'BOOK67890',
                    'genre' => BookGenreEnum::FANTASY->value,
                    'status' => BookStatusEnum::AVAILABLE->value
                ],
                'success' => true,
                'message' => 'Livro criado com sucesso'
            ]);

        $this->assertDatabaseHas('books', [
            'title' => 'O Senhor dos Anéis',
            'author' => 'J.R.R. Tolkien',
            'registration_number' => 'BOOK67890',
            'genre' => BookGenreEnum::FANTASY->value,
            'status' => BookStatusEnum::AVAILABLE->value
        ]);
    }


    public function test_it_validates_required_fields_when_creating_book()
    {
        // Arrange
        $bookData = [
            'title' => '',
            'author' => '',
            'registration_number' => '',
            'genre' => 'invalid-genre'
        ];

        // Act
        $response = $this->postJson('/api/v1/books', $bookData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'author', 'registration_number', 'genre']);
    }


    public function test_it_validates_unique_registration_number_when_creating_book()
    {
        // Arrange
        $existingBook = Book::factory()->create([
            'registration_number' => 'BOOK-EXISTING'
        ]);

        $bookData = [
            'title' => 'Novo Livro',
            'author' => 'Novo Autor',
            'registration_number' => 'BOOK-EXISTING',
            'genre' => BookGenreEnum::FICTION->value
        ];

        // Act
        $response = $this->postJson('/api/v1/books', $bookData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['registration_number']);
    }


    public function test_it_can_update_a_book()
    {
        // Arrange
        $book = Book::factory()->create([
            'title' => 'Memórias Póstumas de Brás Cubas',
            'author' => 'Machado de Assis',
            'genre' => BookGenreEnum::FICTION->value,
            'status' => BookStatusEnum::AVAILABLE->value
        ]);

        $updateData = [
            'title' => 'Memórias Póstumas',
            'genre' => BookGenreEnum::FICTION->value
        ];

        // Act
        $response = $this->putJson("/api/v1/books/{$book->id}", $updateData);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'message',
                'success'
            ])
            ->assertJson([
                'data' => [
                    'id' => $book->id,
                    'title' => 'Memórias Póstumas',
                    'author' => 'Machado de Assis',
                    'genre' => BookGenreEnum::FICTION->value,
                    'status' => BookStatusEnum::AVAILABLE->value
                ],
                'success' => true,
                'message' => 'Livro atualizado com sucesso'
            ]);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Memórias Póstumas',
            'author' => 'Machado de Assis',
            'genre' => BookGenreEnum::FICTION->value,
            'status' => BookStatusEnum::AVAILABLE->value
        ]);
    }


    public function test_it_validates_unique_registration_number_when_updating_book()
    {
        // Arrange
        $book1 = Book::factory()->create([
            'registration_number' => 'BOOK-ONE'
        ]);

        $book2 = Book::factory()->create([
            'registration_number' => 'BOOK-TWO'
        ]);

        $updateData = [
            'registration_number' => 'BOOK-TWO'
        ];

        // Act
        $response = $this->putJson("/api/v1/books/{$book1->id}", $updateData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['registration_number']);
    }


    public function test_it_can_delete_a_book()
    {
        // Arrange
        $book = Book::factory()->create();

        // Act
        $response = $this->deleteJson("/api/v1/books/{$book->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Livro removido com sucesso'
            ]);

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }
}
