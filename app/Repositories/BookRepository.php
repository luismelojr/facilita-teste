<?php

namespace App\Repositories;

use App\Enums\BookGenreEnum;
use App\Enums\BookStatusEnum;
use App\Interfaces\BookRepositoryInterface;
use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;

class BookRepository extends BaseRepository implements BookRepositoryInterface
{
    /**
     * BookRepository constructor.
     *
     * @param Book $model
     */
    public function __construct(Book $model)
    {
        parent::__construct($model);
    }

    /**
     * @inheritdoc
     */
    public function findByRegistrationNumber(string $registrationNumber): ?Book
    {
        return $this->model->where('registration_number', $registrationNumber)->first();
    }

    /**
     * @inheritdoc
     */
    public function getByStatus(BookStatusEnum $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    /**
     * @inheritdoc
     */
    public function getByGenre(BookGenreEnum $genre): Collection
    {
        return $this->model->where('genre', $genre)->get();
    }

    /**
     * @inheritdoc
     */
    public function searchByTitleOrAuthor(string $search): Collection
    {
        return $this->model->where('title', 'like', "%{$search}%")
            ->orWhere('author', 'like', "%{$search}%")
            ->get();
    }

    /**
     * @inheritdoc
     */
    public function updateStatus(int $bookId, BookStatusEnum $status): bool
    {
        $book = $this->getById($bookId);
        return $book->update(['status' => $status]);
    }
}
