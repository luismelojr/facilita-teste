<?php

namespace Tests\Unit\Repositories;

use App\Enums\BookGenreEnum;
use App\Enums\BookStatusEnum;
use App\Models\Book;
use App\Repositories\BookRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private BookRepository $bookRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bookRepository = new BookRepository(new Book());
    }


    public function test_it_can_get_all_books()
    {

        Book::factory()->count(3)->create();


        $result = $this->bookRepository->getAll();


        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
    }


    public function test_it_can_get_book_by_id()
    {

        $book = Book::factory()->create([
            'title' => 'O Senhor dos Anéis',
            'author' => 'J.R.R. Tolkien',
            'registration_number' => 'B001',
            'genre' => BookGenreEnum::FANTASY->value,
            'status' => BookStatusEnum::AVAILABLE->value
        ]);


        $foundBook = $this->bookRepository->getById($book->id);


        $this->assertInstanceOf(Book::class, $foundBook);
        $this->assertEquals($book->id, $foundBook->id);
        $this->assertEquals('O Senhor dos Anéis', $foundBook->title);
        $this->assertEquals('J.R.R. Tolkien', $foundBook->author);
        $this->assertEquals('B001', $foundBook->registration_number);
        $this->assertEquals(BookGenreEnum::FANTASY, $foundBook->genre);
        $this->assertEquals(BookStatusEnum::AVAILABLE, $foundBook->status);
    }


    public function test_it_can_create_a_book()
    {

        $bookData = [
            'title' => 'Dom Casmurro',
            'author' => 'Machado de Assis',
            'registration_number' => 'B002',
            'genre' => BookGenreEnum::FICTION->value,
            'status' => BookStatusEnum::AVAILABLE->value
        ];


        $book = $this->bookRepository->create($bookData);


        $this->assertInstanceOf(Book::class, $book);
        $this->assertDatabaseHas('books', [
            'title' => 'Dom Casmurro',
            'author' => 'Machado de Assis',
            'registration_number' => 'B002',
            'genre' => BookGenreEnum::FICTION->value,
            'status' => BookStatusEnum::AVAILABLE->value
        ]);
    }


    public function test_it_can_update_a_book()
    {

        $book = Book::factory()->create([
            'title' => 'A Metamorfose',
            'author' => 'Franz Kafka',
            'genre' => BookGenreEnum::FICTION->value
        ]);

        $updateData = [
            'title' => 'A Metamorfose e Outros Contos',
            'genre' => BookGenreEnum::FICTION->value
        ];


        $result = $this->bookRepository->update($book->id, $updateData);


        $this->assertTrue($result);
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'A Metamorfose e Outros Contos',
            'author' => 'Franz Kafka',
            'genre' => BookGenreEnum::FICTION->value
        ]);
    }


    public function test_it_can_delete_a_book()
    {

        $book = Book::factory()->create();


        $result = $this->bookRepository->delete($book->id);


        $this->assertTrue($result);
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }


    public function test_it_can_find_book_by_registration_number()
    {

        $book = Book::factory()->create([
            'registration_number' => 'B123'
        ]);

        Book::factory()->count(2)->create();


        $foundBook = $this->bookRepository->findByRegistrationNumber('B123');


        $this->assertInstanceOf(Book::class, $foundBook);
        $this->assertEquals($book->id, $foundBook->id);
        $this->assertEquals('B123', $foundBook->registration_number);
    }


    public function test_it_returns_null_when_registration_number_not_found()
    {

        Book::factory()->create([
            'registration_number' => 'B123'
        ]);


        $result = $this->bookRepository->findByRegistrationNumber('B999');


        $this->assertNull($result);
    }


    public function test_it_can_get_books_by_status()
    {

        Book::factory()->count(2)->available()->create();
        Book::factory()->count(3)->borrowed()->create();


        $availableBooks = $this->bookRepository->getByStatus(BookStatusEnum::AVAILABLE);
        $borrowedBooks = $this->bookRepository->getByStatus(BookStatusEnum::BORROWED);


        $this->assertInstanceOf(Collection::class, $availableBooks);
        $this->assertCount(2, $availableBooks);

        $this->assertInstanceOf(Collection::class, $borrowedBooks);
        $this->assertCount(3, $borrowedBooks);

        foreach ($availableBooks as $book) {
            $this->assertEquals(BookStatusEnum::AVAILABLE, $book->status);
        }

        foreach ($borrowedBooks as $book) {
            $this->assertEquals(BookStatusEnum::BORROWED, $book->status);
        }
    }


    public function test_it_can_get_books_by_genre()
    {

        Book::factory()->count(2)->fantasy()->create();
        Book::factory()->count(3)->scienceFiction()->create();


        $fantasyBooks = $this->bookRepository->getByGenre(BookGenreEnum::FANTASY);
        $scifiBooks = $this->bookRepository->getByGenre(BookGenreEnum::SCIENCE_FICTION);


        $this->assertInstanceOf(Collection::class, $fantasyBooks);
        $this->assertCount(2, $fantasyBooks);

        $this->assertInstanceOf(Collection::class, $scifiBooks);
        $this->assertCount(3, $scifiBooks);

        foreach ($fantasyBooks as $book) {
            $this->assertEquals(BookGenreEnum::FANTASY, $book->genre);
        }

        foreach ($scifiBooks as $book) {
            $this->assertEquals(BookGenreEnum::SCIENCE_FICTION, $book->genre);
        }
    }


    public function test_it_can_search_books_by_title_or_author()
    {

        Book::factory()->create([
            'title' => 'O Programador Pragmático',
            'author' => 'Andrew Hunt'
        ]);

        Book::factory()->create([
            'title' => 'Clean Code',
            'author' => 'Robert C. Martin'
        ]);

        Book::factory()->create([
            'title' => 'Domain-Driven Design',
            'author' => 'Eric Evans'
        ]);

        Book::factory()->create([
            'title' => 'Refactoring',
            'author' => 'Martin Fowler'
        ]);


        $searchByTitle = $this->bookRepository->searchByTitleOrAuthor('Code');
        $searchByAuthor = $this->bookRepository->searchByTitleOrAuthor('Martin');


        $this->assertInstanceOf(Collection::class, $searchByTitle);
        $this->assertCount(1, $searchByTitle);
        $this->assertEquals('Clean Code', $searchByTitle->first()->title);

        $this->assertInstanceOf(Collection::class, $searchByAuthor);
        $this->assertCount(2, $searchByAuthor);
    }


    public function test_it_can_update_book_status()
    {

        $book = Book::factory()->available()->create();


        $result = $this->bookRepository->updateStatus($book->id, BookStatusEnum::BORROWED);


        $this->assertTrue($result);
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'status' => BookStatusEnum::BORROWED->value
        ]);
    }


    public function test_it_can_find_by_criteria()
    {

        Book::factory()->create([
            'title' => 'Livro de Matemática',
            'author' => 'João Silva'
        ]);

        Book::factory()->create([
            'title' => 'Livro de Português',
            'author' => 'Maria Souza'
        ]);

        Book::factory()->create([
            'title' => 'Livro de História',
            'author' => 'Pedro Santos'
        ]);


        $criteria = [
            ['title', 'like', '%Matemática%']
        ];

        $books = $this->bookRepository->findByCriteria($criteria);


        $this->assertInstanceOf(Collection::class, $books);
        $this->assertCount(1, $books);
        $this->assertEquals('Livro de Matemática', $books->first()->title);
    }
}
