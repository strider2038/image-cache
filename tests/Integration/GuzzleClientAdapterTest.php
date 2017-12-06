<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Integration;

use GuzzleHttp\Client;
use Strider2038\ImgCache\Core\GuzzleClientAdapter;
use Strider2038\ImgCache\Core\Http\Response;
use Strider2038\ImgCache\Core\Streaming\StreamFactory;
use Strider2038\ImgCache\Enum\HttpMethodEnum;
use Strider2038\ImgCache\Tests\Support\IntegrationTestCase;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class GuzzleClientAdapterTest extends IntegrationTestCase
{
    private const URI = 'https://example.com';

    /** @test */
    public function request_givenGetMethodAndExampleUri_responseReturned(): void
    {
        $client = new Client();
        $streamFactory = new StreamFactory();
        $adapter = new GuzzleClientAdapter($client, $streamFactory);

        $response = $adapter->request(HttpMethodEnum::GET, self::URI);

        $this->assertInstanceOf(Response::class, $response);
    }
}
