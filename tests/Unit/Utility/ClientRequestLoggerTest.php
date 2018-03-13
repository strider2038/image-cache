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

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Strider2038\ImgCache\Utility\ClientRequestLogger;

class ClientRequestLoggerTest extends TestCase
{
    private const REQUEST_METHOD = 'request_method';
    private const REQUEST_URI = 'request_uri';
    private const REMOTE_ADDRESS = 'remote_address';
    private const REFERRER = 'referrer';
    private const USER_AGENT = 'user_agent';
    private const SERVER_NAME = 'server_name';

    /** @var LoggerInterface */
    private $logger;

    protected function setUp(): void
    {
        $this->logger = \Phake::mock(LoggerInterface::class);
    }

    /** @test */
    public function run_givenLoggerAndServerConfiguration_logInfoIsCalled(): void
    {
        $requestLogger = new ClientRequestLogger($this->logger, [
            'REQUEST_METHOD' => self::REQUEST_METHOD,
            'REQUEST_URI' => self::REQUEST_URI,
            'REMOTE_ADDR' => self::REMOTE_ADDRESS,
            'HTTP_REFERER' => self::REFERRER,
            'HTTP_USER_AGENT' => self::USER_AGENT,
            'SERVER_NAME' => self::SERVER_NAME,
        ]);

        $requestLogger->run();

        $message = $this->assertLogger_info_isCalledOnceAndReturnsMessage();
        $this->assertMessageHasSubstring($message, self::REQUEST_METHOD);
        $this->assertMessageHasSubstring($message, self::REQUEST_URI);
        $this->assertMessageHasSubstring($message, self::REMOTE_ADDRESS);
        $this->assertMessageHasSubstring($message, self::REFERRER);
        $this->assertMessageHasSubstring($message, self::USER_AGENT);
        $this->assertMessageHasSubstring($message, self::SERVER_NAME);
    }

    private function assertLogger_info_isCalledOnceAndReturnsMessage(): string
    {
        \Phake::verify($this->logger, \Phake::times(1))->info(\Phake::capture($message));

        return $message;
    }

    private function assertMessageHasSubstring(string $message, string $substring): void
    {
        $this->assertRegExp(sprintf('/%s/i', $substring), $message);
    }
}
