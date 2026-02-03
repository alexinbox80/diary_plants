<?php

namespace App\Domain\ValueObject\Enum;

enum Currency: string
{
    case USD = 'USD';
    case EUR = 'EUR';
    case RUR = 'RUR';
}
