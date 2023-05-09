<?php

namespace App\Service\Transaction;

use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\UserRepositoryInterface;

class TransactionFileReader
{
    public function __construct(protected UserRepositoryInterface $userRepository)
    {
    }

    /**
     * @param  string $filePath
     * @return array<Transaction>
     */
    public function read(string $filePath): array
    {
        $rows = file($filePath);

        $transactions = [];
        foreach ($rows as $row) {
            $values = str_getcsv($row);

            $userId = $values[1];
            $user = $this->userRepository->find($userId);
            if (!isset($user)) {
                $user = User::of($values[2], $userId);
                $this->userRepository->save($user);
            }

            $transactions[] = Transaction::of(
                $values[0],
                $user,
                $values[3],
                $values[4],
                $values[5],
            );
        }

        return $transactions;
    }
}
