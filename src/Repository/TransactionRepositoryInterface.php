<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\ValueObject\TransactionType;
use Carbon\CarbonInterface;

interface TransactionRepositoryInterface extends RepositoryInterface
{
    public function getUserTransactionsInDatePeriod(
        TransactionType $type,
        User $user,
        CarbonInterface $start,
        CarbonInterface $end
    );
}
