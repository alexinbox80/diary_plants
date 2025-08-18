<?php

namespace App\Domain\Model;

enum Currency: string
{
    case USD = 'USD';
    case EUR = 'EUR';
    case RUR = 'RUR';
}
