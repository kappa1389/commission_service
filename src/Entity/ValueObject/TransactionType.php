<?php

namespace App\Entity\ValueObject;

enum TransactionType: string
{
    case DEPOSIT = 'deposit';
    case WITHDRAW = 'withdraw';
}
