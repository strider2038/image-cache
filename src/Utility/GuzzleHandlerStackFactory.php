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

use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Psr\Log\LoggerInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class GuzzleHandlerStackFactory
{
    private const REQUEST_LOG_FORMAT = 'Guzzle request: HTTP/{version} {method} {uri}';
    private const RESPONSE_LOG_FORMAT = 'Guzzle response: {code} {phrase}';
    private const REQUEST_LOGGER_HANDLER_NAME = 'request_logger';
    private const RESPONSE_LOGGER_HANDLER_NAME = 'response_logger';

    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function createLoggerStack(): HandlerStack
    {
        $stack = HandlerStack::create();

        $stack->push(
            $this->createLoggerHandler(self::RESPONSE_LOG_FORMAT),
            self::RESPONSE_LOGGER_HANDLER_NAME
        );

        $stack->push(
            $this->createLoggerHandler(self::REQUEST_LOG_FORMAT),
            self::REQUEST_LOGGER_HANDLER_NAME
        );

        return $stack;
    }

    private function createLoggerHandler(string $format): callable
    {
        return Middleware::log($this->logger, new MessageFormatter($format));
    }
}
