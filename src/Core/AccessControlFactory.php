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
class AccessControlFactory
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct()
    {
        $this->logger = new NullLogger();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function createAccessControlByToken(string $token): AccessControlInterface
    {
        if ($token === '') {
            $accessControl = new ReadOnlyAccessControl();
            $this->logger->debug('Read only access control is used.');
        } else {
            $accessControl = new BearerWriteAccessControl($token);
            $this->logger->debug('Bearer token access control is used.');
            if (\strlen($token) < 16) {
                $this->logger->warning('Token is not secure. The recommended length of the token must be more than 16 characters.');
            }
        }

        return $accessControl;
    }
}
