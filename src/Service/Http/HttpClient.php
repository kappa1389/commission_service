<?php

namespace App\Service\Http;

use Exception;
use GuzzleHttp\Client;

class HttpClient
{
    public function __construct(protected Client $client)
    {
    }

    public function get(string $url, array $headers = [], $query = []): Response
    {
        $message = null;
        $body = null;
        try {
            $response = $this->client->get(
                $url,
                [
                    'headers' => $headers,
                    'query' => $query
                ]
            );
            $body = $response->getBody();
            $status = $response->getStatusCode();
        } catch (Exception $exception) {
            $message = $exception->getMessage();
            $status = $exception->getCode();
        }

        return new Response(
            $status,
            $body,
            $message
        );
    }
}
