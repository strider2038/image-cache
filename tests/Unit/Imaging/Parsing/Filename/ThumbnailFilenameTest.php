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
use Strider2038\ImgCache\Imaging\Parsing\Filename\ThumbnailFilename;
use Strider2038\ImgCache\Utility\EntityValidator;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;
use Strider2038\ImgCache\Utility\ViolationFormatter;

class ThumbnailFilenameTest extends TestCase
{
    private const VALUE = 'a.jpg';
    private const MASK = 'thumbnail_mask';
    private const PROCESSING_CONFIGURATION = 'processing_configuration';
    private const THUMBNAIL_FILENAME_ID = 'thumbnail filename';

    /** @var EntityValidatorInterface */
    private $validator;

    protected function setUp(): void
    {
        $this->validator = new EntityValidator(new ViolationFormatter());
    }

    /** @test */
    public function construct_givenProperties_propertiesAreSet(): void
    {
        $filename = $this->createThumbnailFilename();

        $this->assertEquals(self::VALUE, $filename->getValue());
        $this->assertEquals(self::MASK, $filename->getMask());
        $this->assertEquals(self::PROCESSING_CONFIGURATION, $filename->getProcessingConfiguration());
    }

    /** @test */
    public function getId_emptyParameters_idReturned(): void
    {
        $filename = $this->createThumbnailFilename();

        $id = $filename->getId();

        $this->assertEquals(self::THUMBNAIL_FILENAME_ID, $id);
    }

    /**
     * @test
     * @param string $processingConfiguration
     * @param bool $expectedHasProcessingConfiguration
     * @dataProvider processingConfigurationProvider
     */
    public function hasProcessingConfiguration_givenProcessingConfiguration_expectedValueReturned(
        string $processingConfiguration,
        bool $expectedHasProcessingConfiguration
    ): void {
        $filename = $this->createThumbnailFilename(self::VALUE, self::MASK, $processingConfiguration);

        $hasProcessingConfiguration = $filename->hasProcessingConfiguration();

        $this->assertEquals($expectedHasProcessingConfiguration, $hasProcessingConfiguration);
    }

    public function processingConfigurationProvider(): array
    {
        return [
            ['', false],
            ['_', true]
        ];
    }

    /**
     * @test
     * @dataProvider valueProvider
     * @param string $value
     * @param int $violationsCount
     */
    public function validate_givenFilename_violationsReturned(string $value, int $violationsCount): void
    {
        $filename = $this->createThumbnailFilename($value);

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
            /* 25 */ ['i_.jpeg', 1],
        ];
    }

    private function createThumbnailFilename(
        $value = self::VALUE,
        $mask = self::MASK,
        $processingConfiguration = self::PROCESSING_CONFIGURATION
    ): ThumbnailFilename {
        return new ThumbnailFilename($value, $mask, $processingConfiguration);
    }
}
