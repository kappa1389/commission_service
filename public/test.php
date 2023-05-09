<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Config\CommissionFeeConfig;
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

// We can use a service container for creating objects and managing dependencies

$fileReader = new TransactionFileReader(new InMemoryUserRepository());
$transactionRepository = new InMemoryTransactionRepository();
$client = new Client();
$httpClient = new HttpClient($client);
$currencyRateProvider = new PSCurrencyRateProvider($httpClient);
$currencyConverter = new CurrencyConverter($currencyRateProvider);
$commissionCalculationStrategies = [
    new DepositCalculationStrategy(CommissionFeeConfig::DEPOSIT_COMMISSION_FEE),
    new PrivateUserWithdrawCalculationStrategy(
        $transactionRepository,
        $currencyConverter,
        CommissionFeeConfig::PRIVATE_USER_FREE_WITHDRAW_TRANSACTIONS_COUNT,
        CommissionFeeConfig::PRIVATE_USER_FREE_WITHDRAW_TRANSACTIONS_AMOUNT_IN_EURO,
        CommissionFeeConfig::PRIVATE_USER_WITHDRAW_COMMISSION_FEE
    ),
    new BusinessUserWithdrawCalculationStrategy(CommissionFeeConfig::BUSINESS_USER_WITHDRAW_COMMISSION_FEE)
];

$calculationStrategyDiscoveryService = new CalculationStrategyDiscoveryService($commissionCalculationStrategies);
$commissionCalculator = new CommissionCalculator($calculationStrategyDiscoveryService);

$transactions = $fileReader->read($argv[1]);
$transactionRepository->saveMany($transactions);

foreach ($transactions as $transaction) {
    echo $commissionCalculator->calculate($transaction);
    print "\n";
}
