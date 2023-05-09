<?php

namespace App\Service\Commission\Calculator;

use App\Entity\Transaction;
use App\Exceptions\UnsupportedTransactionException;

class CalculationStrategyDiscoveryService
{
    /**
     * @param array<CommissionCalculationStrategyInterface> $calculationStrategies
     */
    public function __construct(private array $calculationStrategies)
    {
    }

    /**
     * @throws UnsupportedTransactionException
     */
    public function discover(Transaction $transaction): CommissionCalculationStrategyInterface
    {
        foreach ($this->calculationStrategies as $calculatorStrategy) {
            if ($calculatorStrategy->supports($transaction)) {
                return $calculatorStrategy;
            }
        }

        throw new UnsupportedTransactionException('Transaction is not supported');
    }
}
