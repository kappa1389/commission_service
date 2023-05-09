<?php

namespace App\Service\Commission\Calculator\Strategy;

use App\Entity\Transaction;
use App\Service\Commission\Calculator\CommissionCalculationStrategyInterface;

class BusinessUserWithdrawCalculationStrategy implements CommissionCalculationStrategyInterface
{
    public function __construct(
        private float $commissionFee
    ) {
    }

    public function supports(Transaction $transaction): bool
    {
        return $transaction->isWithdraw() && $transaction->getUser()->isBusiness();
    }

    public function calculate(Transaction $transaction): float
    {
        return ($transaction->getAmount() * $this->commissionFee) / 100;
    }
}
