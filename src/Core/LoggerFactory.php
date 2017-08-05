<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Core;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;


/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class LoggerFactory
{
    const LOG_NAME_DEFAULT = 'runtime.log';
    const LOG_DIRECTORY_DEFAULT = '/var/log/imgcache/';

    /** @var string */
    private $logDirectory;

    /** @var bool */
    private $dryRun;

    public function __construct(string $logDirectory = self::LOG_DIRECTORY_DEFAULT, bool $dryRun = false)
    {
        $this->logDirectory = $logDirectory;
        $this->dryRun = $dryRun;
    }

    public function createLogger(string $logName = self::LOG_NAME_DEFAULT, int $logLevel = Logger::INFO): LoggerInterface
    {
        if ($this->dryRun) {
            $handler = new NullHandler(Logger::EMERGENCY);
        } else {
            $handler = new StreamHandler($this->logDirectory, $logLevel);
        }

        $lineFormatter = new LineFormatter("[%datetime%] %level_name%: %message%\n");
        $handler->setFormatter($lineFormatter);

        return new Logger($logName, [$handler]);
    }
}