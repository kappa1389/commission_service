<?php

namespace App\Entity\ValueObject;

enum TransactionType: string
{
    case DEPOSIT = 'deposit';
    case WITHDRAW = 'withdraw';

    public function isDeposit(): bool
    {
        return $this === self::DEPOSIT;
    }

    public function isWithdraw(): bool
    {
        return $this === self::WITHDRAW;
    }
}
