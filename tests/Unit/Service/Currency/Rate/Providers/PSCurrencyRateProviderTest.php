<?php

namespace Test\Unit\Service\Currency\Rate\Providers;

use App\Entity\ValueObject\Currency;
use App\Service\Currency\Rate\Providers\PS\PSCurrencyRateProvider;
use App\Service\Http\HttpClient;
use App\Service\Http\Response;
use Mockery;
use Test\Unit\BaseUnitTest;

class PSCurrencyRateProviderTest extends BaseUnitTest
{
    public function testConvertCorrectly()
    {
        $client = Mockery::mock(HttpClient::class);
        $body = '{"rates": {"USD":"2", "EUR":"1", "JPY": "6"}}';
        $client->shouldReceive('get')->andReturn(new Response(200, $body));

        $sut = new PSCurrencyRateProvider($client);

        $actual = $sut->calculateRate(Currency::USD, Currency::JPY);

        $expected = 3;
        self::assertEquals($expected, $actual);
    }
}
