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

    public function assertLogger_Info_IsCalledOnce(LoggerInterface $logger, string $message = null): void
    {
        $params = $message ?? \Phake::anyParameters();
        \Phake::verify($logger, \Phake::times(1))->info($params);
    }

    public function assertLogger_Info_IsCalledTimes(LoggerInterface $logger, int $times): void
    {
        \Phake::verify($logger, \Phake::times($times))->info(\Phake::anyParameters());
    }

    public function assertLogger_Error_IsCalledOnce(LoggerInterface $logger, string $message = null): void
    {
        $params = $message ?? \Phake::anyParameters();
        \Phake::verify($logger, \Phake::times(1))->error($params);
    }
}