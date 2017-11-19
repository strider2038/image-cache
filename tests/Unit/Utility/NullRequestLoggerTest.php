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

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Utility\NullRequestLogger;
use Strider2038\ImgCache\Utility\RequestLoggerInterface;

class NullRequestLoggerTest extends TestCase
{
    /** @test */
    public function logCurrentRequest_noParameters_nothingHappened(): void
    {
        $logger = new NullRequestLogger();

        $logger->logCurrentRequest();

        $this->assertInstanceOf(RequestLoggerInterface::class, $logger);
    }
}
