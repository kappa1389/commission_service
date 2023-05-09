<?php

namespace App\Service\Currency\Converter;

use App\Entity\ValueObject\Currency;
use App\Exceptions\RemoteServerException;
use App\Service\Currency\Rate\CurrencyRateProviderInterface;

class CurrencyConverter
{
    public function __construct(protected CurrencyRateProviderInterface $currencyRateProvider)
    {
    }

    /**
     * @throws RemoteServerException
     */
    public function convert(float $amount, Currency $from, Currency $to): float
    {
        $rate = $this->currencyRateProvider->calculateRate($from, $to);

        return $amount * $rate;
    }
}
