<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Support;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Strider2038\ImgCache\Enum\HttpMethodEnum;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class AcceptanceTestCase extends FunctionalTestCase
{
    /** @var \GuzzleHttp\Client */
    private $client;
    /** @var string */
    private $accessToken = '';

    protected function setUp(): void
    {
        parent::setUp();

        exec('rm -rf ' . self::RUNTIME_DIRECTORY . '/tests/acceptance/storage/*');
        exec('rm -rf ' . self::RUNTIME_DIRECTORY . '/tests/acceptance/web/*');
        exec('chown www-data:www-data ' . self::RUNTIME_DIRECTORY . '/tests/acceptance/storage');
        exec('chown www-data:www-data ' . self::RUNTIME_DIRECTORY . '/tests/acceptance/web');

        $host = getenv('ACCEPTANCE_HOST');

        $this->client = new Client([
            'base_uri' => $host,
            'timeout' => 5,
            'allow_redirects' => false,
            'http_errors' => false,
        ]);
    }

    protected function givenAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    protected function sendRequest(string $method, string $uri, $body = null)
    {
        return $this->client->request($method, $uri, [
            'body' => $body,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
            ],
        ]);
    }

    protected function sendGET(string $uri): ResponseInterface
    {
        return $this->client->request(HttpMethodEnum::GET, $uri);
    }

    protected function sendPOST(string $uri, $body = null): ResponseInterface
    {
        return $this->sendRequest(HttpMethodEnum::POST, $uri, $body);
    }

    protected function sendPUT(string $uri, $body = null): ResponseInterface
    {
        return $this->sendRequest(HttpMethodEnum::PUT, $uri, $body);
    }

    protected function sendDELETE(string $uri): ResponseInterface
    {
        return $this->sendRequest(HttpMethodEnum::DELETE, $uri);
    }
}
