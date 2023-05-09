<?php

namespace App\Service\Currency\Rate\Providers\PS;

use App\Config\PSCurrencyRateConfig;
use App\Entity\ValueObject\Currency;
use App\Exceptions\RemoteServerException;
use App\Service\Currency\Rate\CurrencyRateProviderInterface;
use App\Service\Http\HttpClient;
use App\Service\Http\Response;

class PSCurrencyRateProvider implements CurrencyRateProviderInterface
{
    public function __construct(protected HttpClient $client)
    {
    }

    /**
     * @throws RemoteServerException
     */
    public function calculateRate(Currency $from, Currency $to): float
    {
        $response = $this->callApi();

        $body = json_decode($response->body(), true);

        $rates = $body['rates'];

        $targetToEuroRate = 1 / $rates[$from->value];

        return $targetToEuroRate * $rates[$to->value];
    }

    /**
     * @throws RemoteServerException
     */
    protected function callApi(): Response
    {
        $response = $this->client->get(
            sprintf('%s%s', PSCurrencyRateConfig::BASE_URL, PSCurrencyRateConfig::RATES_URI),
        );

        if (!$response->isSuccessful()) {
            throw new RemoteServerException($response->message());
        }

        return $response;
    }
}
