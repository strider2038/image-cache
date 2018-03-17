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
    private const ACCESS_CONTROL_TOKEN = 'Bearer acceptance-testing-token';
    /** @var \GuzzleHttp\Client */
    protected $client;

    /** @var string */
    protected $host;
    
    protected function setUp(): void
    {
        parent::setUp();

        $this->host = getenv('ACCEPTANCE_HOST');

        $this->client = new Client([
            'base_uri' => $this->host,
            'timeout' => 5,
            'allow_redirects' => false,
            'http_errors' => false,
        ]);
    }

    protected function sendGET(string $uri): ResponseInterface
    {
        return $this->client->request(HttpMethodEnum::GET, $uri);
    }

    protected function sendPOST(string $uri, $body = null): ResponseInterface
    {
        return $this->client->request(HttpMethodEnum::POST, $uri, [
            'body' => $body,
            'headers' => [
                'Authorization' => self::ACCESS_CONTROL_TOKEN,
            ],
        ]);
    }
}
