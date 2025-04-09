<?php

namespace App\DTO;

use App\Enums\BookGenreEnum;
use App\Enums\BookStatusEnum;

class BookDTO
{
    /**
     * BookDTO constructor.
     *
     * @param string $title
     * @param string $author
     * @param string $registration_number
     * @param BookGenreEnum $genre
     * @param BookStatusEnum|null $status
     */
    public function __construct(
        public readonly string $title,
        public readonly string $author,
        public readonly string $registration_number,
        public readonly BookGenreEnum $genre,
        public readonly ?BookStatusEnum $status = null
    ) {
    }

    /**
     * Criar DTO a partir de um array
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            author: $data['author'],
            registration_number: $data['registration_number'],
            genre: is_string($data['genre']) ? BookGenreEnum::from($data['genre']) : $data['genre'],
            status: isset($data['status'])
                ? (is_string($data['status']) ? BookStatusEnum::from($data['status']) : $data['status'])
                : null
        );
    }

    /**
     * Converter para array
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = [
            'title' => $this->title,
            'author' => $this->author,
            'registration_number' => $this->registration_number,
            'genre' => $this->genre->value,
        ];

        if ($this->status) {
            $data['status'] = $this->status->value;
        }

        return $data;
    }
}
