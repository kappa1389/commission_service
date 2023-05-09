<?php

namespace Test\Integration;

use App\Repository\InMemory\InMemoryTransactionRepository;
use App\Repository\InMemory\InMemoryUserRepository;
use App\Service\Commission\Calculator\CalculationStrategyDiscoveryService;
use App\Service\Commission\Calculator\CommissionCalculator;
use App\Service\Commission\Calculator\Strategy\BusinessUserWithdrawCalculationStrategy;
use App\Service\Commission\Calculator\Strategy\DepositCalculationStrategy;
use App\Service\Commission\Calculator\Strategy\PrivateUserWithdrawCalculationStrategy;
use App\Service\Currency\Converter\CurrencyConverter;
use App\Service\Currency\Rate\Providers\PS\PSCurrencyRateProvider;
use App\Service\Http\HttpClient;
use App\Service\Transaction\TransactionFileReader;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Test\Unit\BaseUnitTest;

class IntegratedSystemTest extends BaseIntegrationTest
{
    public function testShouldWorkCorrectlyForSampleData()
    {
        $fileReader = new TransactionFileReader(new InMemoryUserRepository());
        $transactionRepository = new InMemoryTransactionRepository();
        $client = Mockery::mock(Client::class);
        $httpClient = new HttpClient($client);
        $currencyRateProvider = new PSCurrencyRateProvider($httpClient);
        $currencyConverter = new CurrencyConverter($currencyRateProvider);

        $transactions = $fileReader->read('tests/Integration/sample.csv');
        $transactionRepository->saveMany($transactions);

        $strategies = [
            new DepositCalculationStrategy(0.03),
            new PrivateUserWithdrawCalculationStrategy(
                $transactionRepository,
                $currencyConverter,
                3,
                1000,
                0.3
            ),
            new BusinessUserWithdrawCalculationStrategy(0.5)
        ];

        $calculationStrategyDiscoveryService = new CalculationStrategyDiscoveryService($strategies);
        $commissionCalculator = new CommissionCalculator($calculationStrategyDiscoveryService);

        $body = '{"rates": {"USD":"1.1497", "JPY": "129.53", "EUR":"1"}}';
        $response = new Response(200, body: $body);
        $client
            ->shouldReceive('get')
            ->andReturn($response);

        self::assertEquals(0.60, $commissionCalculator->calculate($transactions[0]));
        self::assertEquals(3.00, $commissionCalculator->calculate($transactions[1]));
        self::assertEquals(0.00, $commissionCalculator->calculate($transactions[2]));
        self::assertEquals(0.06, $commissionCalculator->calculate($transactions[3]));
        self::assertEquals(1.50, $commissionCalculator->calculate($transactions[4]));
        self::assertEquals(0, $commissionCalculator->calculate($transactions[5]));
        self::assertEquals(0.70, $commissionCalculator->calculate($transactions[6]));
        self::assertEquals(0.30, $commissionCalculator->calculate($transactions[7]));
        self::assertEquals(0.30, $commissionCalculator->calculate($transactions[8]));
        self::assertEquals(3.00, $commissionCalculator->calculate($transactions[9]));
        self::assertEquals(0.00, $commissionCalculator->calculate($transactions[10]));
        self::assertEquals(0.00, $commissionCalculator->calculate($transactions[11]));
        self::assertEquals(8611.41, $commissionCalculator->calculate($transactions[12]));
    }
}
