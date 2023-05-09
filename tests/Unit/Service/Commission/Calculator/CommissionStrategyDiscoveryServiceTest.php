<?php

namespace Unit\Service\Commission\Calculator;

use App\Exceptions\UnsupportedTransactionException;
use App\Service\Commission\Calculator\CalculationStrategyDiscoveryService;
use App\Service\Commission\Calculator\CommissionCalculationStrategyInterface;
use Mockery;
use Test\Builders\TransactionBuilder;
use Test\Unit\BaseUnitTest;

class CommissionStrategyDiscoveryServiceTest extends BaseUnitTest
{
    public function testShouldReturnStrategyIfItSupportsTransaction()
    {
        $strategy1 = Mockery::mock(CommissionCalculationStrategyInterface::class);
        $strategy2 = Mockery::mock(CommissionCalculationStrategyInterface::class);
        $transaction = (new TransactionBuilder())->build();

        $sut = new CalculationStrategyDiscoveryService([$strategy1, $strategy2]);

        $strategy1->shouldReceive('supports')->with($transaction)->andReturnFalse();
        $strategy2->shouldReceive('supports')->with($transaction)->andReturnTrue();

        $actual = $sut->discover($transaction);

        self::assertSame($strategy2, $actual);
    }

    public function testShouldThrowExceptionIfNoStrategySupportsTransaction()
    {
        $strategy1 = Mockery::mock(CommissionCalculationStrategyInterface::class);
        $transaction = (new TransactionBuilder())->build();

        $sut = new CalculationStrategyDiscoveryService([$strategy1]);

        $strategy1->shouldReceive('supports')->with($transaction)->andReturnFalse();

        self::expectException(UnsupportedTransactionException::class);

        $sut->discover($transaction);
    }
}
