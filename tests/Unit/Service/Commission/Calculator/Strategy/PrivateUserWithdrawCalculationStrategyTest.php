<?php

namespace Test\Unit\Service\Commission\Calculator\Strategy;

use App\Entity\User;
use App\Entity\ValueObject\UserType;
use App\Repository\TransactionRepositoryInterface;
use App\Service\Commission\Calculator\Strategy\PrivateUserWithdrawCalculationStrategy;
use App\Service\Currency\Converter\CurrencyConverter;
use Mockery;
use Test\Builders\TransactionBuilder;
use Test\Unit\BaseUnitTest;

class PrivateUserWithdrawCalculationStrategyTest extends BaseUnitTest
{
    public function testShouldChargeWholeTransactionIfFreeTransactionsCountExceeds()
    {
        $transactionRepo = Mockery::mock(TransactionRepositoryInterface::class);
        $currencyConverter = Mockery::spy(CurrencyConverter::class);

        $fee = 0.1;
        $freeTransactionsCountPerWeek = 2;
        $freeTransactionAmountInEuro = 1000;
        $sut = new PrivateUserWithdrawCalculationStrategy(
            $transactionRepo,
            $currencyConverter,
            $freeTransactionsCountPerWeek,
            $freeTransactionAmountInEuro,
            $fee
        );

        $user = new User(UserType::BUSINESS, 1);

        $transactionBuilder = new TransactionBuilder();

        $tran1 = $transactionBuilder
            ->refresh()
            ->withDate('2023-05-01')
            ->withUser($user)
            ->build();

        $tran2 = $transactionBuilder
            ->refresh()
            ->withDate('2023-05-02')
            ->withUser($user)
            ->build();

        $tran3 = $transactionBuilder
            ->refresh()
            ->withDate('2023-05-04')
            ->withUser($user)
            ->build();

        $transactionRepo
            ->shouldReceive('getUserTransactionsInDatePeriod')
            ->andReturn([$tran1, $tran2, $tran3]);

        $commission = $sut->calculate($tran3);
        $expected = ($tran3->getAmount() * $fee) / 100;
        self::assertEquals($expected, $commission);
    }

    public function testShouldChargeWholeTransactionIfFreeTransactionsAmountExceeds()
    {
        $transactionRepo = Mockery::mock(TransactionRepositoryInterface::class);
        $currencyConverter = Mockery::spy(CurrencyConverter::class);

        $fee = 0.1;
        $freeTransactionsCountPerWeek = 2;
        $freeTransactionAmountInEuro = 1000;
        $sut = new PrivateUserWithdrawCalculationStrategy(
            $transactionRepo,
            $currencyConverter,
            $freeTransactionsCountPerWeek,
            $freeTransactionAmountInEuro,
            $fee
        );

        $user = new User(UserType::BUSINESS, 1);

        $transactionBuilder = new TransactionBuilder();

        $tran1 = $transactionBuilder
            ->refresh()
            ->withDate('2023-05-01')
            ->withUser($user)
            ->withAmount(1200)
            ->build();
        $tran2 = $transactionBuilder
            ->refresh()
            ->withDate('2023-05-02')
            ->withUser($user)
            ->build();

        $transactionRepo
            ->shouldReceive('getUserTransactionsInDatePeriod')
            ->andReturn([$tran1, $tran2]);

        $commission = $sut->calculate($tran2);

        $expected = ($tran2->getAmount() * $fee) / 100;
        self::assertEquals($expected, $commission);
    }

    public function testShouldNotChargeTransactionIfItMeetsFreeOfChargeConditions()
    {
        $transactionRepo = Mockery::mock(TransactionRepositoryInterface::class);
        $currencyConverter = Mockery::mock(CurrencyConverter::class);

        $fee = 0.1;
        $freeTransactionsCountPerWeek = 2;
        $freeTransactionAmountInEuro = 1000;
        $sut = new PrivateUserWithdrawCalculationStrategy(
            $transactionRepo,
            $currencyConverter,
            $freeTransactionsCountPerWeek,
            $freeTransactionAmountInEuro,
            $fee
        );

        $user = new User(UserType::BUSINESS, 1);

        $transactionBuilder = new TransactionBuilder();

        $tran1 = $transactionBuilder
            ->refresh()
            ->withDate('2023-05-01')
            ->withUser($user)
            ->withAmount(100)
            ->build();
        $tran2 = $transactionBuilder
            ->refresh()
            ->withDate('2023-05-02')
            ->withUser($user)
            ->withAmount(200)
            ->build();

        $transactionRepo
            ->shouldReceive('getUserTransactionsInDatePeriod')
            ->andReturn([$tran1, $tran2]);

        $currencyConverter
            ->shouldReceive('convert')
            ->andReturnUsing(
                function ($amount, $from, $to) {
                    return $amount;
                }
            );

        $commission = $sut->calculate($tran2);

        $expected = 0;
        self::assertEquals($expected, $commission);
    }

    public function testShouldOnlyChargePartOfTransactionAmountThatIsNotFreeOfCharge()
    {
        $freeTransactionAmount = 1000;
        $transactionRepo = Mockery::mock(TransactionRepositoryInterface::class);
        $currencyConverter = Mockery::mock(CurrencyConverter::class);

        $fee = 0.1;
        $freeTransactionsCountPerWeek = 2;
        $freeTransactionAmountInEuro = 1000;
        $sut = new PrivateUserWithdrawCalculationStrategy(
            $transactionRepo,
            $currencyConverter,
            $freeTransactionsCountPerWeek,
            $freeTransactionAmountInEuro,
            $fee
        );

        $user = new User(UserType::BUSINESS, 1);

        $transactionBuilder = new TransactionBuilder();

        $tran1 = $transactionBuilder
            ->refresh()
            ->withDate('2023-05-01')
            ->withUser($user)
            ->withAmount(800)
            ->build();
        $tran2 = $transactionBuilder
            ->refresh()
            ->withDate('2023-05-02')
            ->withUser($user)
            ->withAmount(300)
            ->build();

        $transactionRepo
            ->shouldReceive('getUserTransactionsInDatePeriod')
            ->andReturn([$tran1, $tran2]);

        $currencyConverter
            ->shouldReceive('convert')
            ->andReturnUsing(
                function ($amount, $from, $to) {
                    return $amount;
                }
            );

        $commission = $sut->calculate($tran2);

        $chargeableAmount = ($tran1->getAmount() + $tran2->getAmount()) - $freeTransactionAmount;
        $expected = ($fee * $chargeableAmount) / 100;
        self::assertEquals($expected, $commission);
    }

    public function testShouldOnlySupportWithdrawTypeForPrivateUser()
    {
        $transactionRepo = Mockery::mock(TransactionRepositoryInterface::class);
        $currencyConverter = Mockery::mock(CurrencyConverter::class);

        $fee = 0.1;
        $freeTransactionsCountPerWeek = 2;
        $freeTransactionAmountInEuro = 1000;
        $sut = new PrivateUserWithdrawCalculationStrategy(
            $transactionRepo,
            $currencyConverter,
            $freeTransactionsCountPerWeek,
            $freeTransactionAmountInEuro,
            $fee
        );

        $transactionBuilder = new TransactionBuilder();

        $supported = $transactionBuilder
            ->refresh()
            ->withUser(new User(UserType::PRIVATE))
            ->withType('withdraw')
            ->build();

        $notSupported1 = $transactionBuilder
            ->refresh()
            ->withUser(new User(UserType::PRIVATE))
            ->withType('deposit')
            ->build();

        $notSupported2 = $transactionBuilder
            ->refresh()
            ->withUser(new User(UserType::BUSINESS))
            ->withType('withdraw')
            ->build();

        self::assertTrue($sut->supports($supported));
        self::assertFalse($sut->supports($notSupported1));
        self::assertFalse($sut->supports($notSupported2));
    }
}
