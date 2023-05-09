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
    // This is a simple cache in order to avoid multiple calls
    // to the rate-api in the same session
    protected Response $cachedResponse;

    public function __construct(private HttpClient $client)
    {
    }

    /**
     * @throws RemoteServerException
     */
    public function calculateRate(Currency $from, Currency $to): float
    {
        if (!isset($this->cachedResponse)) {
            $this->callApi();
        }

        $body = json_decode($this->cachedResponse->body(), true);

        $rates = $body['rates'];

        $targetToEuroRate = 1 / $rates[$from->value];

        return $targetToEuroRate * $rates[$to->value];
    }

    /**
     * @throws RemoteServerException
     */
    protected function callApi(): void
    {
        $response = $this->client->get(
            sprintf('%s%s', PSCurrencyRateConfig::BASE_URL, PSCurrencyRateConfig::RATES_URI),
        );

        if (!$response->isSuccessful()) {
            throw new RemoteServerException($response->message());
        }

        $this->cachedResponse = $response;
    }
}
