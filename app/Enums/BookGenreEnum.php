<?php

namespace App\Enums;

enum BookGenreEnum : string
{
    case FICTION = 'Fiction';
    case ROMANCE = 'Romance';
    case FANTASY = 'Fantasy';
    case ADVENTURE = 'Adventure';
    case SCIENCE_FICTION = 'Science Fiction';
    case HORROR = 'Horror';
    case BIOGRAPHY = 'Biography';
    case HISTORY = 'History';
    case SCIENCE = 'Science';
    case MYSTERY = 'Mystery';
    case THRILLER = 'Thriller';
    case OTHER = 'Other';
}
