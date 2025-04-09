<?php

namespace Tests\Unit\Services;

use App\Enums\BookStatusEnum;
use App\Enums\BookGenreEnum;
use App\Interfaces\BookRepositoryInterface;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

class BookServiceTest extends TestCase
{
    protected $bookRepository;
    protected $bookService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookRepository = Mockery::mock(BookRepositoryInterface::class);
        $this->bookService = new BookService($this->bookRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_books(): void
    {

        $books = new Collection([
            new Book(['id' => 1, 'title' => 'Book 1']),
            new Book(['id' => 2, 'title' => 'Book 2'])
        ]);

        $this->bookRepository->shouldReceive('getAll')
            ->once()
            ->andReturn($books);


        $result = $this->bookService->getAllBooks();


        $this->assertCount(2, $result);
    }

    public function test_get_book_by_id(): void
    {

        $book = new Book(['id' => 1, 'title' => 'Test Book']);
        $bookId = 1;

        $this->bookRepository->shouldReceive('getById')
            ->once()
            ->with($bookId)
            ->andReturn($book);


        $result = $this->bookService->getBookById($bookId);


        $this->assertSame($book, $result);
    }

    public function test_create_book(): void
    {

        $bookData = [
            'title' => 'New Book',
            'author' => 'John Doe',
            'registration_number' => 'BOOK123',
            'genre' => BookGenreEnum::FICTION,
            'status' => BookStatusEnum::AVAILABLE
        ];

        $createdBook = new Book($bookData);

        $this->bookRepository->shouldReceive('create')
            ->once()
            ->with($bookData)
            ->andReturn($createdBook);


        $result = $this->bookService->createBook($bookData);


        $this->assertSame($createdBook, $result);
    }

    public function test_update_book(): void
    {

        $bookId = 1;
        $updateData = [
            'title' => 'Updated Book Title',
            'author' => 'Jane Doe'
        ];

        $this->bookRepository->shouldReceive('update')
            ->once()
            ->with($bookId, $updateData)
            ->andReturn(true);


        $result = $this->bookService->updateBook($bookId, $updateData);


        $this->assertTrue($result);
    }

    public function test_delete_book(): void
    {

        $bookId = 1;

        $this->bookRepository->shouldReceive('delete')
            ->once()
            ->with($bookId)
            ->andReturn(true);


        $result = $this->bookService->deleteBook($bookId);


        $this->assertTrue($result);
    }

    public function test_get_available_books(): void
    {

        $availableBooks = new Collection([
            new Book(['id' => 1, 'status' => BookStatusEnum::AVAILABLE]),
            new Book(['id' => 2, 'status' => BookStatusEnum::AVAILABLE])
        ]);

        $this->bookRepository->shouldReceive('getByStatus')
            ->once()
            ->with(BookStatusEnum::AVAILABLE)
            ->andReturn($availableBooks);


        $result = $this->bookService->getAvailableBooks();


        $this->assertCount(2, $result);
        $result->each(function($book) {
            $this->assertEquals(BookStatusEnum::AVAILABLE, $book->status);
        });
    }

    public function test_get_books_by_genre(): void
    {

        $genre = BookGenreEnum::SCIENCE_FICTION;
        $scienceFictionBooks = new Collection([
            new Book(['id' => 1, 'genre' => BookGenreEnum::SCIENCE_FICTION]),
            new Book(['id' => 2, 'genre' => BookGenreEnum::SCIENCE_FICTION])
        ]);

        $this->bookRepository->shouldReceive('getByGenre')
            ->once()
            ->with($genre)
            ->andReturn($scienceFictionBooks);


        $result = $this->bookService->getBooksByGenre($genre);


        $this->assertCount(2, $result);
        $result->each(function($book) use ($genre) {
            $this->assertEquals($genre, $book->genre);
        });
    }

    public function test_search_books(): void
    {

        $searchTerm = 'Programming';
        $matchingBooks = new Collection([
            new Book(['id' => 1, 'title' => 'Programming 101']),
            new Book(['id' => 2, 'title' => 'Advanced Programming'])
        ]);

        $this->bookRepository->shouldReceive('searchByTitleOrAuthor')
            ->once()
            ->with($searchTerm)
            ->andReturn($matchingBooks);


        $result = $this->bookService->searchBooks($searchTerm);


        $this->assertCount(2, $result);
    }

    public function test_mark_as_borrowed(): void
    {

        $bookId = 1;

        $this->bookRepository->shouldReceive('updateStatus')
            ->once()
            ->with($bookId, BookStatusEnum::BORROWED)
            ->andReturn(true);


        $result = $this->bookService->markAsBorrowed($bookId);


        $this->assertTrue($result);
    }

    public function test_mark_as_available(): void
    {

        $bookId = 1;

        $this->bookRepository->shouldReceive('updateStatus')
            ->once()
            ->with($bookId, BookStatusEnum::AVAILABLE)
            ->andReturn(true);


        $result = $this->bookService->markAsAvailable($bookId);


        $this->assertTrue($result);
    }

    public function test_is_registration_number_taken_when_not_used(): void
    {

        $registrationNumber = 'BOOK123';

        $this->bookRepository->shouldReceive('findByRegistrationNumber')
            ->once()
            ->with($registrationNumber)
            ->andReturn(null);


        $result = $this->bookService->isRegistrationNumberTaken($registrationNumber);


        $this->assertFalse($result);
    }

    public function test_is_registration_number_taken_when_used(): void
    {

        $registrationNumber = 'BOOK123';
        $existingBook = new Book(['id' => 1, 'registration_number' => $registrationNumber]);

        $this->bookRepository->shouldReceive('findByRegistrationNumber')
            ->once()
            ->with($registrationNumber)
            ->andReturn($existingBook);


        $result = $this->bookService->isRegistrationNumberTaken($registrationNumber);


        $this->assertTrue($result);
    }

    public function test_is_registration_number_taken_with_exception_when_not_same_book(): void
    {

        $registrationNumber = 'BOOK123';
        $existingBook = new Book(['id' => 1, 'registration_number' => $registrationNumber]);
        $exceptBookId = 2;

        $this->bookRepository->shouldReceive('findByRegistrationNumber')
            ->once()
            ->with($registrationNumber)
            ->andReturn($existingBook);


        $result = $this->bookService->isRegistrationNumberTaken($registrationNumber, $exceptBookId);


        $this->assertTrue($result);
    }

    public function test_is_registration_number_taken_with_exception_when_same_book(): void
    {

        $registrationNumber = 'BOOK123';
        $bookId = 1;
        $existingBook = Mockery::mock(Book::class);
        $existingBook->shouldReceive('getAttribute')->with('id')->andReturn($bookId);
        $existingBook->shouldReceive('getAttribute')->with('registration_number')->andReturn($registrationNumber);

        $this->bookRepository->shouldReceive('findByRegistrationNumber')
            ->once()
            ->with($registrationNumber)
            ->andReturn($existingBook);


        $result = $this->bookService->isRegistrationNumberTaken($registrationNumber, $bookId);


        $this->assertFalse($result, "Should return false when checking a book's own registration number");
    }
}
