<?php

namespace App\DTO;

use App\Enums\LoanStatusEnum;
use Carbon\Carbon;

class LoanDTO
{
    /**
     * LoanDTO constructor.
     *
     * @param int $user_id
     * @param int $book_id
     * @param Carbon $due_date
     * @param LoanStatusEnum|null $status
     */
    public function __construct(
        public readonly int $user_id,
        public readonly int $book_id,
        public readonly Carbon $due_date,
        public readonly ?LoanStatusEnum $status = null
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
            user_id: $data['user_id'],
            book_id: $data['book_id'],
            due_date: $data['due_date'] instanceof Carbon
                ? $data['due_date']
                : Carbon::parse($data['due_date']),
            status: isset($data['status'])
                ? (is_string($data['status']) ? LoanStatusEnum::from($data['status']) : $data['status'])
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
            'user_id' => $this->user_id,
            'book_id' => $this->book_id,
            'due_date' => $this->due_date->format('Y-m-d'),
        ];

        if ($this->status) {
            $data['status'] = $this->status->value;
        }

        return $data;
    }
}
