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

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Utility\LoggerFactory;
use Strider2038\ImgCache\Utility\RuntimeLoggerProcessor;

class LoggerFactoryTest extends TestCase
{
    private const LOG_NAME_DEFAULT = 'runtime.log';
    private const LOG_DIRECTORY_DEFAULT = '/var/log/imgcache/';
    private const LOG_FILENAME_DEFAULT = self::LOG_DIRECTORY_DEFAULT . self::LOG_NAME_DEFAULT;
    private const LOG_LEVEL_DEFAULT = Logger::INFO;
    private const LOG_NAME_CUSTOM = 'custom.log';
    private const LOG_DIRECTORY_CUSTOM = '/tmp/';
    private const LOG_FILENAME_CUSTOM = self::LOG_DIRECTORY_CUSTOM . self::LOG_NAME_CUSTOM;
    private const LOG_LEVEL_CUSTOM = Logger::DEBUG;

    /** @test */
    public function createLogger_givenDefaultLogNameAndDirectoryAndLevel_logNameAndDirectoryAreSet(): void
    {
        $loggerFactory = new LoggerFactory(0);

        /** @var Logger $logger */
        $logger = $loggerFactory->createLogger();

        $this->verifyLogger($logger,self::LOG_NAME_DEFAULT, self::LOG_LEVEL_DEFAULT, self::LOG_FILENAME_DEFAULT);
    }

    /** @test */
    public function createLogger_givenCustomLogNameAndDirectoryAndLevel_logNameAndDirectoryAreSet(): void
    {
        $loggerFactory = new LoggerFactory(0, self::LOG_DIRECTORY_CUSTOM);

        /** @var Logger $logger */
        $logger = $loggerFactory->createLogger(self::LOG_NAME_CUSTOM, self::LOG_LEVEL_CUSTOM);

        $this->verifyLogger($logger,self::LOG_NAME_CUSTOM, self::LOG_LEVEL_CUSTOM, self::LOG_FILENAME_CUSTOM);
    }

    /** @test */
    public function createLogger_givenDefaultParametersAndDryRunIsOn_LoggerHasNullHandler(): void
    {
        $loggerFactory = new LoggerFactory(0, self::LOG_DIRECTORY_DEFAULT, true);

        /** @var Logger $logger */
        $logger = $loggerFactory->createLogger();

        $handlers = $logger->getHandlers();
        $this->assertCount(1, $handlers);
        $this->assertInstanceOf(NullHandler::class, $handlers[0]);
    }

    private function verifyLogger(Logger $logger, string $name, int $level, string $filename): void
    {
        $this->assertEquals($name, $logger->getName());

        $handlers = $logger->getHandlers();
        $this->assertCount(1, $handlers);
        /** @var StreamHandler $handler */
        $handler = $handlers[0];
        $this->assertEquals($level, $handler->getLevel());
        $this->assertEquals($filename, $handler->getUrl());

        $processors = $logger->getProcessors();
        $this->assertCount(2, $processors);
        $this->assertInstanceOf(RuntimeLoggerProcessor::class, $processors[0]);
        $this->assertInstanceOf(UidProcessor::class, $processors[1]);
    }
}
