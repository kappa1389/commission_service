<?php

namespace Test\Unit\Service\Http;

use App\Service\Http\HttpClient;
use Exception;
use GuzzleHttp\Client;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Test\Unit\BaseUnitTest;

class HttpClientTest extends BaseUnitTest
{
    private ResponseInterface|LegacyMockInterface|MockInterface $guzzleResponse;
    private Client|LegacyMockInterface|MockInterface $client;
    private HttpClient $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guzzleResponse = Mockery::mock(ResponseInterface::class);
        $this->client = Mockery::mock(Client::class);
        $this->sut = new HttpClient($this->client);
    }

    public function testGetMethodShouldReturnResponseIfNoExceptionHappens()
    {
        $url = 'test-url';
        $query = ['foo' => 'bar'];
        $headers = ['key' => 'value'];
        $responseBody = Mockery::mock(StreamInterface::class);
        $statusCode = 200;

        $this->client
            ->expects('get')
            ->with($url, ['headers' => $headers, 'query' => $query])
            ->andReturn($this->guzzleResponse);

        $this->guzzleResponse->expects('getBody')->withNoArgs()->andReturn($responseBody);
        $this->guzzleResponse->expects('getStatusCode')->withNoArgs()->andReturn($statusCode);

        $actual = $this->sut->get($url, $headers, $query);
        self::assertEquals($statusCode, $actual->status());
        self::assertEquals($responseBody, $actual->body());
    }

    public function testGetMethodShouldReturnResponseIfClientExceptionHappens()
    {
        $url = 'test-url';
        $query = ['foo' => 'bar'];
        $headers = ['key' => 'value'];
        $errorMessage = 'error';
        $statusCode = 500;

        $this->client
            ->expects('get')
            ->with($url, ['headers' => $headers, 'query' => $query])
            ->andThrow(Exception::class, $errorMessage, $statusCode);

        $actual = $this->sut->get($url, $headers, $query);
        self::assertEquals($statusCode, $actual->status());
        self::assertEquals($errorMessage, $actual->message());
        self::assertNull($actual->body());
    }
}
