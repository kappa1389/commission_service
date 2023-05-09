<?php

namespace App\Repository\InMemory;

use App\Entity\Transaction;
use App\Entity\User;
use App\Entity\ValueObject\TransactionType;
use App\Repository\TransactionRepositoryInterface;
use Carbon\CarbonInterface;

class InMemoryTransactionRepository extends AbstractRepository implements TransactionRepositoryInterface
{
    /**
     * @param  TransactionType $type
     * @param  User            $user
     * @param  CarbonInterface $start
     * @param  CarbonInterface $end
     * @return array<Transaction>
     */
    public function getUserTransactionsInDatePeriod(
        TransactionType $type,
        User $user,
        CarbonInterface $start,
        CarbonInterface $end
    ): array {
        $result = [];
        $transactions = $this->getByUserAndType($user, $type);
        $sorted = $this->sortByDateDesc($transactions);

        foreach ($sorted as $transaction) {
            if ($transaction->getDate() <= $end && $transaction->getDate() >= $start) {
                $result[] = $transaction;
            }

            if ($transaction->getDate() < $start) {
                break;
            }
        }

        return $result;
    }

    /**
     * @return array<Transaction>
     */
    public function getByUserAndType(User $user, TransactionType $type): array
    {
        return
            array_values(
                array_filter(
                    $this->entities,
                    fn(Transaction $transaction): bool =>
                        $transaction->belongsTo($user)
                        && $transaction->getType() === $type
                )
            );
    }

    /**
     * @param  array<int, Transaction> $entities
     * @return array<int, Transaction>
     */
    protected function sortByDateDesc(array $entities): array
    {
        usort(
            $entities,
            fn(Transaction $transaction) => $transaction->getDate()->toDateString()
        );

        return $entities;
    }
}
