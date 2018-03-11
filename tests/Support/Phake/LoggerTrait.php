<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Support\Phake;

use Psr\Log\LoggerInterface;


/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
trait LoggerTrait
{
    public function givenLogger(): LoggerInterface
    {
        return \Phake::mock(LoggerInterface::class);
    }

    public function assertLogger_debug_isCalledTimes(LoggerInterface $logger, int $times): void
    {
        \Phake::verify($logger, \Phake::times($times))->debug(\Phake::anyParameters());
    }

    public function assertLogger_info_isCalledOnce(LoggerInterface $logger, string $message = null): void
    {
        $params = $message ?? \Phake::anyParameters();
        \Phake::verify($logger, \Phake::times(1))->info($params);
    }

    public function assertLogger_info_isCalledTimes(LoggerInterface $logger, int $times): void
    {
        \Phake::verify($logger, \Phake::times($times))->info(\Phake::anyParameters());
    }

    public function assertLogger_warning_isCalledOnce(LoggerInterface $logger, string $message = null): void
    {
        $params = $message ?? \Phake::anyParameters();
        \Phake::verify($logger, \Phake::times(1))->warning($params);
    }

    public function assertLogger_warning_isNeverCalled(LoggerInterface $logger): void
    {
        \Phake::verify($logger, \Phake::times(0))->warning(\Phake::anyParameters());
    }

    public function assertLogger_error_isCalledOnce(LoggerInterface $logger, string $message = null): void
    {
        $params = $message ?? \Phake::anyParameters();
        \Phake::verify($logger, \Phake::times(1))->error($params);
    }

    public function assertLogger_critical_isCalledOnce(LoggerInterface $logger, string $message = null): void
    {
        $params = $message ?? \Phake::anyParameters();
        \Phake::verify($logger, \Phake::times(1))->critical($params);
    }
}