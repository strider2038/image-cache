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

use Psr\Log\LoggerInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class RequestLogger implements RequestLoggerInterface
{
    /** @var LoggerInterface */
    private $logger;

    /** @var array */
    private $serverConfiguration;

    public function __construct(LoggerInterface $logger, array $serverConfiguration)
    {
        $this->logger = $logger;
        $this->serverConfiguration = $serverConfiguration;
    }

    public function logCurrentRequest(): void
    {
        $this->logger->info(sprintf(
            'Processing request %s %s from ip %s, referrer %s, user agent %s by server %s',
            $this->serverConfiguration['REQUEST_METHOD'] ?? '',
            $this->serverConfiguration['REQUEST_URI'] ?? '',
            $this->serverConfiguration['REMOTE_ADDR'] ?? '',
            $this->serverConfiguration['HTTP_REFERER'] ?? '',
            $this->serverConfiguration['HTTP_USER_AGENT'] ?? '',
            $this->serverConfiguration['SERVER_NAME'] ?? ''
        ));
    }
}
