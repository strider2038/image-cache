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
use Psr\Log\LoggerInterface;
use Strider2038\ImgCache\Utility\GuzzleHandlerStackFactory;

class GuzzleHandlerStackFactoryTest extends TestCase
{
    private const REQUEST_LOGGER_HANDLER_NAME = 'request_logger';
    private const RESPONSE_LOGGER_HANDLER_NAME = 'response_logger';

    /** @var LoggerInterface */
    private $logger;

    protected function setUp(): void
    {
        $this->logger = \Phake::mock(LoggerInterface::class);
    }

    /** @test */
    public function createLoggerStack_givenLogger_stackCreatedAndReturned(): void
    {
        $factory = new GuzzleHandlerStackFactory($this->logger);

        $stack = $factory->createLoggerStack();

        $this->assertInstanceOf(HandlerStack::class, $stack);
        $this->assertStackHasHandlerWithName($stack, self::REQUEST_LOGGER_HANDLER_NAME);
        $this->assertStackHasHandlerWithName($stack, self::RESPONSE_LOGGER_HANDLER_NAME);
    }

    private function assertStackHasHandlerWithName(HandlerStack $stack, string $name): void
    {
        $dumpedStack = $stack . '';
        $this->assertRegExp(sprintf("/Name: '%s'/i", $name), $dumpedStack);
    }
}
