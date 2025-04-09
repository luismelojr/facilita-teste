<?php

namespace App\Services;

use App\Enums\BookGenreEnum;
use App\Enums\BookStatusEnum;
use App\Interfaces\BookRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BookService
{
    /**
     * BookService constructor.
     *
     * @param BookRepositoryInterface $bookRepository
     */
    public function __construct(
        protected BookRepositoryInterface $bookRepository
    ){}

    /**
     * Obter todos os livros
     *
     * @return Collection
     */
    public function getAllBooks(): Collection
    {
        return $this->bookRepository->getAll();
    }

    /**
     * Obter livro por ID
     *
     * @param int $bookId
     * @return Model
     */
    public function getBookById(int $bookId): Model
    {
        return $this->bookRepository->getById($bookId);
    }

    /**
     * Criar novo livro
     *
     * @param array $bookData
     * @return Model
     */
    public function createBook(array $bookData): Model
    {
        // Definir status padrão se não fornecido
        if (!isset($bookData['status'])) {
            $bookData['status'] = BookStatusEnum::AVAILABLE;
        }

        return $this->bookRepository->create($bookData);
    }

    /**
     * Atualizar livro existente
     *
     * @param int $bookId
     * @param array $bookData
     * @return bool
     */
    public function updateBook(int $bookId, array $bookData): bool
    {
        return $this->bookRepository->update($bookId, $bookData);
    }

    /**
     * Excluir livro
     *
     * @param int $bookId
     * @return bool
     */
    public function deleteBook(int $bookId): bool
    {
        return $this->bookRepository->delete($bookId);
    }

    /**
     * Obter livros disponíveis
     *
     * @return Collection
     */
    public function getAvailableBooks(): Collection
    {
        return $this->bookRepository->getByStatus(BookStatusEnum::AVAILABLE);
    }

    /**
     * Obter livros por gênero
     *
     * @param BookGenreEnum $genre
     * @return Collection
     */
    public function getBooksByGenre(BookGenreEnum $genre): Collection
    {
        return $this->bookRepository->getByGenre($genre);
    }

    /**
     * Pesquisar livros por título ou autor
     *
     * @param string $searchTerm
     * @return Collection
     */
    public function searchBooks(string $searchTerm): Collection
    {
        return $this->bookRepository->searchByTitleOrAuthor($searchTerm);
    }

    /**
     * Verificar se número de registro já está em uso
     *
     * @param string $registrationNumber
     * @param int|null $exceptBookId
     * @return bool
     */
    public function isRegistrationNumberTaken(string $registrationNumber, ?int $exceptBookId = null): bool
    {
        $book = $this->bookRepository->findByRegistrationNumber($registrationNumber);

        if (!$book) {
            return false;
        }

        if ($exceptBookId && $book->id === $exceptBookId) {
            return false;
        }

        return true;
    }

    /**
     * Marcar livro como emprestado
     *
     * @param int $bookId
     * @return bool
     */
    public function markAsBorrowed(int $bookId): bool
    {
        return $this->bookRepository->updateStatus($bookId, BookStatusEnum::BORROWED);
    }

    /**
     * Marcar livro como disponível
     *
     * @param int $bookId
     * @return bool
     */
    public function markAsAvailable(int $bookId): bool
    {
        return $this->bookRepository->updateStatus($bookId, BookStatusEnum::AVAILABLE);
    }
}
