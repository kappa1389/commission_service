<?php

namespace Test\Unit\Service\Transaction;

use App\Entity\Transaction;
use App\Entity\User;
use App\Entity\ValueObject\Currency;
use App\Entity\ValueObject\TransactionType;
use App\Repository\UserRepositoryInterface;
use App\Service\Transaction\TransactionFileReader;
use Mockery;
use Test\Unit\BaseUnitTest;

class TransactionFileReaderTest extends BaseUnitTest
{
    public function testShouldReadFileAndCreateTransactions()
    {
        $userRepo = Mockery::mock(UserRepositoryInterface::class);
        $sut = new TransactionFileReader($userRepo);

        $userRepo->shouldReceive('find')->twice()->andReturn(null, User::of('private', 1));
        $userRepo->shouldReceive('save')->once();

        $transactions = $sut->read('tests/Unit/Service/Transaction/sample.csv');

        self::assertCount(2, $transactions);

        self::assertInstanceOf(Transaction::class, $transactions[0]);
        self::assertEquals(1200, $transactions[0]->getAmount());
        self::assertEquals(TransactionType::DEPOSIT, $transactions[0]->getType());
        self::assertEquals(Currency::EUR, $transactions[0]->getCurrency());
        self::assertEquals('2023-05-02', $transactions[0]->getDate()->toDateString());

        self::assertInstanceOf(Transaction::class, $transactions[1]);
        self::assertEquals(800, $transactions[1]->getAmount());
        self::assertEquals(TransactionType::WITHDRAW, $transactions[1]->getType());
        self::assertEquals(Currency::USD, $transactions[1]->getCurrency());
        self::assertEquals('2023-05-03', $transactions[1]->getDate()->toDateString());
    }
}
