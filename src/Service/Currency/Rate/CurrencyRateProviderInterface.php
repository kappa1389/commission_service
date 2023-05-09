<?php

namespace App\Service\Currency\Rate;

use App\Entity\ValueObject\Currency;
use App\Exceptions\RemoteServerException;

interface CurrencyRateProviderInterface
{
    /**
     * @throws RemoteServerException
     */
    public function calculateRate(Currency $from, Currency $to): float;
}
