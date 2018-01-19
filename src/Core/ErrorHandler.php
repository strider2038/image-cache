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

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ErrorHandler
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
    }

    public function register(): void
    {
        register_shutdown_function([$this, 'onShutdown'], $this->logger);
        $this->logger->debug('Shutdown function is registered.');
    }

    public static function onShutdown(LoggerInterface $logger = null, array $error = null): void
    {
        $logger = $logger ?? new NullLogger();
        $error = $error ?? error_get_last() ?? [];

        $message = implode(PHP_EOL, array_map(
            function($value, $key) {
                return sprintf('%s: %s', ucfirst($key), $value);
            },
            $error,
            array_keys($error)
        ));

        $logger->critical($message);
    }
}
