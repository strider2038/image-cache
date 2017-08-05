<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Core;

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Strider2038\ImgCache\Core\LoggerFactory;
use PHPUnit\Framework\TestCase;

class LoggerFactoryTest extends TestCase
{
    const LOG_NAME_DEFAULT = 'runtime.log';
    const LOG_DIRECTORY_DEFAULT = '/var/log/imgcache/';
    const LOG_LEVEL_DEFAULT = Logger::INFO;
    const LOG_NAME_CUSTOM = 'custom.log';
    const LOG_DIRECTORY_CUSTOM = '/tmp/';
    const LOG_LEVEL_CUSTOM = Logger::DEBUG;

    public function testCreateLogger_GivenDefaultLogNameAndDirectoryAndLevel_LogNameAndDirectoryAreSet(): void
    {
        $loggerFactory = new LoggerFactory();

        /** @var Logger $logger */
        $logger = $loggerFactory->createLogger();

        $this->verifyLogger($logger,self::LOG_NAME_DEFAULT, self::LOG_LEVEL_DEFAULT, self::LOG_DIRECTORY_DEFAULT);
    }

    public function testCreateLogger_GivenCustomLogNameAndDirectoryAndLevel_LogNameAndDirectoryAreSet(): void
    {
        $loggerFactory = new LoggerFactory(self::LOG_DIRECTORY_CUSTOM);

        /** @var Logger $logger */
        $logger = $loggerFactory->createLogger(self::LOG_NAME_CUSTOM, self::LOG_LEVEL_CUSTOM);

        $this->verifyLogger($logger,self::LOG_NAME_CUSTOM, self::LOG_LEVEL_CUSTOM, self::LOG_DIRECTORY_CUSTOM);
    }

    public function testCreateLogger_GivenDefaultParametersAndDryRunIsOn_LoggerHasNullHandler(): void
    {
        $loggerFactory = new LoggerFactory(self::LOG_DIRECTORY_DEFAULT, true);

        /** @var Logger $logger */
        $logger = $loggerFactory->createLogger();

        $handlers = $logger->getHandlers();
        $this->assertCount(1, $handlers);
        $this->assertInstanceOf(NullHandler::class, $handlers[0]);
    }

    private function verifyLogger(Logger $logger, string $name, int $level, string $directory): void
    {
        $this->assertEquals($name, $logger->getName());
        $handlers = $logger->getHandlers();
        $this->assertCount(1, $handlers);
        /** @var StreamHandler $handler */
        $handler = $handlers[0];
        $this->assertEquals($level, $handler->getLevel());
        $this->assertEquals($directory, $handler->getUrl());
    }
}
