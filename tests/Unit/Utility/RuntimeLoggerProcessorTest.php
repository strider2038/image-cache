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
use Strider2038\ImgCache\Utility\RuntimeLoggerProcessor;

class RuntimeLoggerProcessorTest extends TestCase
{
    private const START_UP_TIME = 1.7684;

    /** @test */
    public function invoke_givenStartUpTime_extraRecordWithRuntimeAdded(): void
    {
        $processor = new RuntimeLoggerProcessor(self::START_UP_TIME);
        $record = [];

        $updatedRecord = $processor($record);

        $this->assertArrayHasKey('extra', $updatedRecord);
        $this->assertArrayHasKey('runtime', $updatedRecord['extra']);
    }
}
