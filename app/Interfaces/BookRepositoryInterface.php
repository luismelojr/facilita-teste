<?php

namespace App\Interfaces;

use App\Enums\BookGenreEnum;
use App\Enums\BookStatusEnum;
use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;

interface BookRepositoryInterface extends RepositoryInterface
{
    /**
     * Encontrar livro pelo número de registro
     *
     * @param string $registrationNumber Número de registro do livro
     * @return Book|null
     */
    public function findByRegistrationNumber(string $registrationNumber): ?Book;

    /**
     * Obter livros por status
     *
     * @param BookStatusEnum $status Status do livro
     * @return Collection
     */
    public function getByStatus(BookStatusEnum $status): Collection;

    /**
     * Obter livros por gênero
     *
     * @param BookGenreEnum $genre Gênero do livro
     * @return Collection
     */
    public function getByGenre(BookGenreEnum $genre): Collection;

    /**
     * Buscar livros pelo título ou autor
     *
     * @param string $search Termo de busca
     * @return Collection
     */
    public function searchByTitleOrAuthor(string $search): Collection;

    /**
     * Atualizar status do livro
     *
     * @param int $bookId ID do livro
     * @param BookStatusEnum $status Novo status
     * @return bool
     */
    public function updateStatus(int $bookId, BookStatusEnum $status): bool;
}
