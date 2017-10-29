<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Enum\ResourceStreamModeEnum;

class ResourceStreamModeEnumTest extends TestCase
{
    private const READ_ONLY = 'rb';
    private const READ_AND_WRITE = 'rb+';
    private const WRITE_ONLY = 'wb';
    private const WRITE_AND_READ = 'wb+';
    private const APPEND_ONLY = 'ab';
    private const APPEND_AND_READ = 'ab+';
    private const WRITE_IF_NOT_EXIST = 'xb';
    private const WRITE_AND_READ_IF_NOT_EXIST = 'xb+';
    private const WRITE_WITHOUT_TRUNCATE = 'cb';
    private const WRITE_AND_READ_WITHOUT_TRUNCATE = 'cb+';

    /**
     * @test
     * @param string $mode
     * @param bool $expectedIsReadable
     * @dataProvider readableModesProvider
     */
    public function isReadable_givenMode_expectedBoolIsReturned(
        string $mode,
        bool $expectedIsReadable
    ): void {
        $enum = new ResourceStreamModeEnum($mode);

        $isReadable = $enum->isReadable();

        $this->assertEquals($expectedIsReadable, $isReadable);
    }

    public function readableModesProvider(): array
    {
        return [
            [self::READ_ONLY, true],
            [self::WRITE_ONLY, false],
        ];
    }

    /**
     * @test
     * @param string $mode
     * @param bool $expectedIsWritable
     * @dataProvider writableModesProvider
     */
    public function isWritable_givenMode_expectedBoolIsReturned(
        string $mode,
        bool $expectedIsWritable
    ): void {
        $enum = new ResourceStreamModeEnum($mode);

        $isWritable = $enum->isWritable();

        $this->assertEquals($expectedIsWritable, $isWritable);
    }

    public function writableModesProvider(): array
    {
        return [
            [self::READ_ONLY, false],
            [self::WRITE_ONLY, true],
        ];
    }
}
