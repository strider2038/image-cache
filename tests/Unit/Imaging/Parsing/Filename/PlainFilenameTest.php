<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Parsing\Filename;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Parsing\Filename\PlainFilename;
use Strider2038\ImgCache\Utility\EntityValidator;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;
use Strider2038\ImgCache\Utility\MetadataReader;
use Strider2038\ImgCache\Utility\Validation\CustomConstraintValidatorFactory;
use Strider2038\ImgCache\Utility\ViolationFormatter;

class PlainFilenameTest extends TestCase
{
    private const VALUE = 'a.jpg';
    private const PLAIN_FILENAME_ID = 'filename';

    /** @var EntityValidatorInterface */
    private $validator;

    protected function setUp(): void
    {
        $this->validator = new EntityValidator(
            new CustomConstraintValidatorFactory(
                new MetadataReader()
            ),
            new ViolationFormatter()
        );
    }

    /** @test */
    public function construct_givenValue_valueSet(): void
    {
        $key = new PlainFilename(self::VALUE);

        $this->assertEquals(self::VALUE, $key->getValue());
    }

    /** @test */
    public function getId_emptyParameters_idReturned(): void
    {
        $plainFilename = new PlainFilename(self::VALUE);

        $id = $plainFilename->getId();

        $this->assertEquals(self::PLAIN_FILENAME_ID, $id);
    }

    /**
     * @test
     * @dataProvider valueProvider
     * @param string $value
     * @param int $violationsCount
     */
    public function validate_givenFilename_violationsReturned(string $value, int $violationsCount): void
    {
        $filename = new PlainFilename($value);

        $violations = $this->validator->validate($filename);

        $this->assertCount($violationsCount, $violations);
    }

    public function valueProvider(): array
    {
        return [
            /*  0 */ ['', 1],
            /*  1 */ ['  ', 2],
            /*  2 */ ['/', 2],
            /*  3 */ [' /', 2],
            /*  4 */ ['//', 2],
            /*  5 */ ['file .jpg', 1],
            /*  6 */ ['кириллица.jpg', 1],
            /*  7 */ ['/path/\1', 2],
            /*  8 */ ['file.err', 1],
            /*  9 */ ['../file.jpg', 2],
            /* 10 */ ['.../i.jpg', 2],
            /* 11 */ ['f../i.jpg', 2],
            /* 12 */ ['/../file.jpg', 2],
            /* 13 */ ['dir.name/f.jpeg', 1],
            /* 14 */ ['/.dir/f.jpg', 1],
            /* 15 */ ['dir/f.jpg', 0],
            /* 16 */ ['dir//f.jpg', 1],
            /* 17 */ ['./f.jpeg', 1],
            /* 18 */ ['/./f.jpeg', 1],
            /* 19 */ ['Img.jpg', 0],
            /* 20 */ ['/path/image.jpg', 0],
            /* 21 */ ['/path/image..jpg', 1],
            /* 22 */ ['/path/image_sz80x100.jpg', 0],
            /* 23 */ ['//path//image_sz80x100.jpg', 1],
            /* 24 */ ['_root/i-2_q95.jpeg', 0],
        ];
    }
}
