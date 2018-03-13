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
use Strider2038\ImgCache\Core\Service\ApplicationServiceInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ClientRequestLogger implements ApplicationServiceInterface
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

    public function run(): void
    {
        $this->logger->info(sprintf(
            'Processing request %s %s from ip %s, referrer "%s", user agent "%s" by server "%s"',
            $this->serverConfiguration['REQUEST_METHOD'] ?? '',
            $this->serverConfiguration['REQUEST_URI'] ?? '',
            $this->serverConfiguration['REMOTE_ADDR'] ?? '',
            $this->serverConfiguration['HTTP_REFERER'] ?? 'undefined',
            $this->serverConfiguration['HTTP_USER_AGENT'] ?? 'undefined',
            $this->serverConfiguration['SERVER_NAME'] ?? ''
        ));
    }
}
