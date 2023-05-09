<?php

namespace App\Service\Commission\Calculator\Strategy;

use App\Entity\Transaction;
use App\Entity\ValueObject\Currency;
use App\Entity\ValueObject\TransactionType;
use App\Repository\TransactionRepositoryInterface;
use App\Service\Commission\Calculator\CommissionCalculationStrategyInterface;
use App\Service\Currency\Converter\CurrencyConverter;

class PrivateUserWithdrawCalculationStrategy implements CommissionCalculationStrategyInterface
{
    public function __construct(
        protected TransactionRepositoryInterface $transactionRepository,
        protected CurrencyConverter $currencyConverter,
        protected int $freeTransactionsCountPerWeek,
        protected int $totalFreeTransactionAmountInEuro,
        protected float $commissionFee
    ) {
    }

    public function supports(Transaction $transaction): bool
    {
        return $transaction->isWithdraw() && $transaction->getUser()->isPrivate();
    }

    public function calculate(Transaction $transaction): float
    {
        $chargeableAmount = $this->calculateChargeableAmount($transaction);

        return ($chargeableAmount * $this->commissionFee) / 100;
    }

    // This method calculates how much of the transaction
    // amount should be charged by commission fee
    protected function calculateChargeableAmount(Transaction $transaction): float
    {
        $transactionsInWeek = $this->transactionRepository
            ->getUserTransactionsInDatePeriod(
                TransactionType::WITHDRAW,
                $transaction->getUser(),
                $transaction->getDate()->clone()->startOfWeek(),
                $transaction->getDate()->clone()->endOfWeek()
            );
        $sortedTransactions = $this->sortByDateAsc($transactionsInWeek);
        $currentTransactionNumber = $this->findTransactionPlace($transaction, $sortedTransactions);

        // If `free weekly transactions count` is exceeded then the whole
        // transaction amount will be charged by commission fee
        if ($currentTransactionNumber > $this->freeTransactionsCountPerWeek) {
            return $transaction->getAmount();
        }

        $previousTransactions = $this->getPreviousTransactions($transaction, $sortedTransactions);
        $totalPreviousTransactionsAmountInEuro = $this->sumOfAmountsInEuro($previousTransactions);
        $remainedFreeTransactionAmountInEuro =
            $this->totalFreeTransactionAmountInEuro
            - $totalPreviousTransactionsAmountInEuro;

        // If `total free weekly transactions amount` is exceeded then the whole
        // transaction amount will be charged by commission fee
        if ($remainedFreeTransactionAmountInEuro <= 0) {
            return $transaction->getAmount();
        }

        $remainedFreeTransactionAmountInOriginalCurrency = $this->currencyConverter
            ->convert(
                $remainedFreeTransactionAmountInEuro,
                Currency::EUR,
                $transaction->getCurrency()
            );

        $chargeableAmount = $transaction->getAmount() - $remainedFreeTransactionAmountInOriginalCurrency;

        return max($chargeableAmount, 0);
    }

    protected function convert(int $totalFreeTransactionAmountPerWeek, Currency $to): float
    {
        return $this->currencyConverter->convert(
            $totalFreeTransactionAmountPerWeek,
            Currency::EUR,
            $to
        );
    }

    /**
     * @param  array<Transaction> $transactions
     * @return array<Transaction>
     */
    protected function sortByDateAsc(array $transactions): array
    {
        usort(
            $transactions,
            fn(Transaction $first, Transaction $second) => $first->getDate() > $second->getDate() ? 1 : 0
        );

        return $transactions;
    }

    /**
     * @param  Transaction   $transaction
     * @param  Transaction[] $sortedTransactions
     * @return ?int
     */
    protected function findTransactionPlace(Transaction $transaction, array $sortedTransactions): ?int
    {
        $i = 0;
        foreach ($sortedTransactions as $sortedTransaction) {
            $i++;
            if ($sortedTransaction->getId() === $transaction->getId()) {
                return $i;
            }
        }

        return null;
    }

    /**
     * @param  Transaction   $transaction
     * @param  Transaction[] $sortedTransactions
     * @return Transaction[]
     */
    protected function getPreviousTransactions(Transaction $transaction, array $sortedTransactions): array
    {
        $result = [];
        foreach ($sortedTransactions as $sortedTransaction) {
            if ($sortedTransaction->getId() === $transaction->getId()) {
                break;
            }
            $result[] = $sortedTransaction;
        }

        return $result;
    }

    /**
     * @param  Transaction[] $transactions
     * @return float
     */
    protected function sumOfAmountsInEuro(array $transactions): float
    {
        return array_reduce(
            $transactions,
            fn(float $sum, Transaction $transaction): float =>
                $sum
                + $this->currencyConverter
                ->convert(
                    $transaction->getAmount(),
                    $transaction->getCurrency(),
                    Currency::EUR
                ),
            0
        );
    }
}
