<?php

namespace App\Models;

use App\Enums\BookGenreEnum;
use App\Enums\BookStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'registration_number',
        'genre',
        'status'
    ];

    protected $casts = [
        'genre' => BookGenreEnum::class,
        'status' => BookStatusEnum::class
    ];

    /**
     * Relacionamento com o modelo de empréstimo.
     *
     * @return HasMany
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Verifica se o livro está disponível para empréstimo.
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->status === BookStatusEnum::AVAILABLE;
    }
}
