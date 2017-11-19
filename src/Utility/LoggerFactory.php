<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Utility;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Log\LoggerInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class LoggerFactory
{
    private const LOG_NAME_DEFAULT = 'runtime.log';
    private const LOG_DIRECTORY_DEFAULT = '/var/log/imgcache/';

    /** @var string */
    private $logDirectory;

    /** @var bool */
    private $dryRun;

    public function __construct(string $logDirectory = self::LOG_DIRECTORY_DEFAULT, bool $dryRun = false)
    {
        $this->logDirectory = rtrim($logDirectory, '/') . '/';
        $this->dryRun = $dryRun;
    }

    public function createLogger(string $logName = self::LOG_NAME_DEFAULT, int $logLevel = Logger::INFO): LoggerInterface
    {
        $logger = new Logger($logName);
        $logger->pushHandler($this->createFileHandler($logName, $logLevel));
        $logger->pushProcessor(new UidProcessor(8));

        return $logger;
    }

    private function createFileHandler(string $logName, int $logLevel): HandlerInterface
    {
        if ($this->dryRun) {
            $handler = new NullHandler(Logger::EMERGENCY);
        } else {
            $handler = new StreamHandler($this->logDirectory . $logName, $logLevel);

            $lineFormatter = new LineFormatter(
                "[%datetime%] [UID: %extra.uid%] %level_name%: %message%\n",
                'Y-m-d H:i:s.u',
                true
            );

            $handler->setFormatter($lineFormatter);
        }

        return $handler;
    }
}
