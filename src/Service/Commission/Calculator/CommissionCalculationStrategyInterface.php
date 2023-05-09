<?php

namespace App\Service\Commission\Calculator;

use App\Entity\Transaction;

interface CommissionCalculationStrategyInterface
{
    public function supports(Transaction $transaction): bool;

    public function calculate(Transaction $transaction): float;
}
