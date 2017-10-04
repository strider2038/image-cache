<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Insertion;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Imaging\Insertion\NullWriter;

class NullWriterTest extends TestCase
{
    private const KEY = 'key';

    /**
     * @test
     * @param string $method
     * @param array $parameters
     * @dataProvider methodAndParametersProvider
     * @expectedException \Strider2038\ImgCache\Exception\NotAllowedException
     * @expectedExceptionCode 405
     * @expectedExceptionMessage Method is not allowed
     */
    public function givenMethod_givenParameters_throwsException(string $method, array $parameters): void
    {
        $writer = new NullWriter();

        call_user_func_array([$writer, $method], $parameters);
    }

    public function methodAndParametersProvider(): array
    {
        return [
            ['exists', [self::KEY]],
            ['insert', [self::KEY, \Phake::mock(StreamInterface::class)]],
            ['delete', [self::KEY]],
            ['getFileMask', [self::KEY]],
        ];
    }
}
