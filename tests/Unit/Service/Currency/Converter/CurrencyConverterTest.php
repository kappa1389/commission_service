<?php

namespace Unit\Service\Currency\Converter;

use App\Entity\ValueObject\Currency;
use App\Service\Currency\Converter\CurrencyConverter;
use App\Service\Currency\Rate\CurrencyRateProviderInterface;
use Mockery;
use Test\Unit\BaseUnitTest;

class CurrencyConverterTest extends BaseUnitTest
{
    public function testShouldConvertBaseOnExchangeRate()
    {
        $rate = 2;
        $amount = 100;
        $rateProvider = Mockery::mock(CurrencyRateProviderInterface::class);

        $sut = new CurrencyConverter($rateProvider);

        $rateProvider->shouldReceive('calculateRate')->andReturn($rate);

        $actual = $sut->convert($amount, Currency::EUR, Currency::USD);

        $expected = $amount * $rate;
        self::assertEquals($expected, $actual);
    }
}
