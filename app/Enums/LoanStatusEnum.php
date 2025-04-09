<?php

namespace App\Enums;

enum LoanStatusEnum : string
{
    case ACTIVE = 'active';
    case DELAYED = 'delayed';
    case RETURNED = 'returned';
}
