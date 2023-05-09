<?php

namespace Unit\Service\Commission\Calculator;

use App\Service\Commission\Calculator\CalculationStrategyDiscoveryService;
use App\Service\Commission\Calculator\CommissionCalculationStrategyInterface;
use App\Service\Commission\Calculator\CommissionCalculator;
use Mockery;
use Test\Builders\TransactionBuilder;
use Test\Unit\BaseUnitTest;

class CommissionCalculatorTest extends BaseUnitTest
{
    public function testShouldGetCommissionFromStrategy()
    {
        $expected = 100;
        $strategyDiscovery = Mockery::mock(CalculationStrategyDiscoveryService::class);
        $strategy = Mockery::mock(CommissionCalculationStrategyInterface::class);

        $sut = new CommissionCalculator($strategyDiscovery);

        $strategyDiscovery->shouldReceive('discover')->andReturn($strategy);
        $strategy->shouldReceive('calculate')->andReturn($expected);

        $transaction = (new TransactionBuilder())->build();

        $actual = $sut->calculate($transaction);

        self::assertEquals($expected, $actual);
    }

    public function testShouldRoundUpTwoDecimalPoints()
    {
        $commission = 1.123;
        $strategy = Mockery::mock(CommissionCalculationStrategyInterface::class);
        $strategyDiscovery = Mockery::mock(CalculationStrategyDiscoveryService::class);

        $sut = new CommissionCalculator($strategyDiscovery);

        $strategyDiscovery->shouldReceive('discover')->andReturn($strategy);
        $strategy->shouldReceive('calculate')->andReturn($commission);

        $transaction = (new TransactionBuilder())->build();

        $actual = $sut->calculate($transaction);

        $expected = 1.13;
        self::assertEquals($expected, $actual);
    }
}
