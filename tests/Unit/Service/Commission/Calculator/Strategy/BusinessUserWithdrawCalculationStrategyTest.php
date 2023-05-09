<?php

namespace Test\Unit\Service\Commission\Calculator\Strategy;

use App\Entity\User;
use App\Entity\ValueObject\UserType;
use App\Service\Commission\Calculator\Strategy\BusinessUserWithdrawCalculationStrategy;
use Test\Builders\TransactionBuilder;
use Test\Unit\BaseUnitTest;

class BusinessUserWithdrawCalculationStrategyTest extends BaseUnitTest
{
    public function testShouldChargeCorrectly()
    {
        $fee = 0.1;

        $sut = new BusinessUserWithdrawCalculationStrategy(
            $fee
        );

        $transactionBuilder = new TransactionBuilder();

        $tran = $transactionBuilder
            ->build();

        $commission = $sut->calculate($tran);
        $expected = ($tran->getAmount() * $fee) / 100;
        self::assertEquals($expected, $commission);
    }

    public function testShouldOnlySupportWithdrawTypeForBusinessUser()
    {
        $fee = 0.1;

        $sut = new BusinessUserWithdrawCalculationStrategy(
            $fee
        );

        $transactionBuilder = new TransactionBuilder();

        $supported = $transactionBuilder
            ->refresh()
            ->withUser(new User(UserType::BUSINESS))
            ->withType('withdraw')
            ->build();

        $notSupported1 = $transactionBuilder
            ->refresh()
            ->withUser(new User(UserType::PRIVATE))
            ->withType('deposit')
            ->build();

        $notSupported2 = $transactionBuilder
            ->refresh()
            ->withUser(new User(UserType::PRIVATE))
            ->withType('withdraw')
            ->build();

        self::assertTrue($sut->supports($supported));
        self::assertFalse($sut->supports($notSupported1));
        self::assertFalse($sut->supports($notSupported2));
    }
}
