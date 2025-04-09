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

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
