<?php

namespace Test\Unit\Service\Commission\Calculator\Strategy;

use App\Service\Commission\Calculator\Strategy\DepositCalculationStrategy;
use Test\Builders\TransactionBuilder;
use Test\Unit\BaseUnitTest;

class DepositCalculationStrategyTest extends BaseUnitTest
{
    public function testShouldChargeCorrectly()
    {
        $fee = 0.1;

        $sut = new DepositCalculationStrategy(
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

        $sut = new DepositCalculationStrategy(
            $fee
        );

        $transactionBuilder = new TransactionBuilder();

        $supported = $transactionBuilder
            ->refresh()
            ->withType('deposit')
            ->build();

        $notSupported1 = $transactionBuilder
            ->refresh()
            ->withType('withdraw')
            ->build();

        self::assertTrue($sut->supports($supported));
        self::assertFalse($sut->supports($notSupported1));
    }
}
