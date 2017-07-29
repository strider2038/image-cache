<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Imaging\Validation;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Validation\KeyValidator;

class KeyValidatorTest extends TestCase
{
    /**
     * @param string $filename
     * @param bool $expectedIsValid
     * @dataProvider publicFilenamesProvider
     */
    public function testIsValidPublicFilename_GivenValue_BoolIsReturned(string $filename, bool $expectedIsValid): void
    {
        $validator = $this->createKeyValidator();

        $isValid = $validator->isValidPublicFilename($filename);

        $this->assertEquals($expectedIsValid, $isValid);
    }

    public function publicFilenamesProvider(): array
    {
        return [
            /*  0 */ ['', false],
            /*  1 */ ['  ', false],
            /*  2 */ ['/', false],
            /*  3 */ [' /', false],
            /*  4 */ ['//', false],
            /*  5 */ ['file .jpg', false],
            /*  6 */ ['кириллица.jpg', false],
            /*  7 */ ['/path/\1', false],
            /*  8 */ ['file.err', true],
            /*  9 */ ['../file.jpg', false],
            /* 10 */ ['.../i.jpg', false],
            /* 11 */ ['f../i.jpg', false],
            /* 12 */ ['/../file.jpg', false],
            /* 13 */ ['dir.name/f.jpeg', false],
            /* 14 */ ['/.dir/f.jpg', false],
            /* 15 */ ['dir/f.ext', true],
            /* 16 */ ['dir//f.ext', false],
            /* 17 */ ['./f.jpeg', false],
            /* 18 */ ['/./f.jpeg', false],
            /* 19 */ ['Img.jpg', true],
            /* 20 */ ['/path/image.JPG', true],
            /* 21 */ ['/path/image..JPG', false],
            /* 22 */ ['/path/image_sz80x100.jpg', true],
            /* 23 */ ['//path//image_sz80x100.jpg', false],
            /* 24 */ ['_root/i_q95.jpeg', true],
        ];
    }

    private function createKeyValidator(): KeyValidator
    {
        $validator = new KeyValidator();

        return $validator;
    }
}
