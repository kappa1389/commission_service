<?php

namespace Test\Unit\Repository\InMemory;

use App\Entity\User;
use App\Entity\ValueObject\TransactionType;
use App\Entity\ValueObject\UserType;
use App\Repository\InMemory\InMemoryTransactionRepository;
use Carbon\Carbon;
use Test\Builders\TransactionBuilder;
use Test\Unit\BaseUnitTest;

class InMemoryTransactionRepositoryTest extends BaseUnitTest
{
    public function testShouldReturnUserTransactionsInDatePeriod()
    {
        $sut = new InMemoryTransactionRepository();

        $user = new User(UserType::BUSINESS, 1);
        $transactionBuilder = new TransactionBuilder();
        $tran1 = $transactionBuilder
            ->refresh()
            ->withDate('2023-01-05')
            ->withType('deposit')
            ->withUser($user)
            ->build();

        $tran2 = $transactionBuilder
            ->refresh()
            ->withDate('2023-01-08')
            ->withType('deposit')
            ->withUser($user)
            ->build();

        $tran3 = $transactionBuilder
            ->refresh()
            ->withDate('2023-01-06')
            ->withType('withdraw')
            ->withUser($user)
            ->build();

        $tran4 = $transactionBuilder
            ->refresh()
            ->withDate('2023-01-06')
            ->withType('deposit')
            ->build();

        $sut->save($tran1);
        $sut->save($tran2);
        $sut->save($tran3);
        $sut->save($tran4);

        $transactions = $sut->getUserTransactionsInDatePeriod(
            TransactionType::DEPOSIT,
            $user,
            Carbon::parse('2023-01-05'),
            Carbon::parse('2023-01-14')
        );

        self::assertCount(2, $transactions);
    }
}
