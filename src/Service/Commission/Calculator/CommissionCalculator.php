<?php

namespace App\Service\Commission\Calculator;

use App\Entity\Transaction;
use App\Exceptions\UnsupportedTransactionException;

class CommissionCalculator
{
    public function __construct(private CalculationStrategyDiscoveryService $calculationStrategyDiscoveryService)
    {
    }

    /**
     * @throws UnsupportedTransactionException
     */
    public function calculate(Transaction $transaction): float
    {
        $calculationStrategy = $this->calculationStrategyDiscoveryService->discover($transaction);

        return ceil($calculationStrategy->calculate($transaction) * 100) / 100;
    }
}
