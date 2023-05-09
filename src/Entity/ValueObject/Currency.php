<?php

namespace App\Entity\ValueObject;

enum Currency: string
{
    case EUR = 'EUR';
    case USD = 'USD';
    case JPY = 'JPY';
}
