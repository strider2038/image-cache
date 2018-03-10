<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Utility;

use GuzzleHttp\HandlerStack;
use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\Streaming\StreamFactoryInterface;
use Strider2038\ImgCache\Utility\GuzzleClientAdapter;
use Strider2038\ImgCache\Utility\GuzzleClientFactory;

class GuzzleClientFactoryTest extends TestCase
{
    /** @var StreamFactoryInterface */
    private $streamFactory;
    /** @var HandlerStack */
    private $handlerStack;

    protected function setUp(): void
    {
        $this->streamFactory = \Phake::mock(StreamFactoryInterface::class);
        $this->handlerStack = \Phake::mock(HandlerStack::class);
    }

    /** @test */
    public function createClient_givenParameters_guzzleClientAdapterCreatedAndReturned(): void
    {
        $factory = $this->createGuzzleClientFactory();

        $client = $factory->createClient();

        $this->assertInstanceOf(GuzzleClientAdapter::class, $client);
    }

    private function createGuzzleClientFactory(): GuzzleClientFactory
    {
        $factory = new GuzzleClientFactory(
            $this->streamFactory,
            $this->handlerStack
        );
        $factory->setUserAgent('User agent');

        return $factory;
    }
}
