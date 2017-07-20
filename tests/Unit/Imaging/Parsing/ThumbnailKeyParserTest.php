<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Imaging\Parsing;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Parsing\ThumbnailKeyParser;
use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;
use Strider2038\ImgCache\Imaging\Transformation\Quality;
use Strider2038\ImgCache\Imaging\Transformation\TransformationInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationsFactoryInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailKeyParserTest extends TestCase
{
    private $factory;
    
    protected function setUp()
    {
        $this->markTestSkipped();

        $this->factory = new class implements TransformationsFactoryInterface {
            public function create(string $config): TransformationInterface
            {
                return new class implements TransformationInterface {
                    public function apply(ProcessingImageInterface $image): void {}
                };
            }
        };
    }
    
    /**
     * @dataProvider incorrectFilenamesProvider
     * @expectedException \Strider2038\ImgCache\Exception\InvalidImageException
     * @expectedExceptionCode 400
     */
    public function testConstruct_FilenameWithIllegalChars_ExceptionThrown(string $filename): void
    {
        new ThumbnailKeyParser($this->factory, $filename);
    }

    public function incorrectFilenamesProvider(): array
    {
        return [
            [''],
            ['/'],
            ['file .jpg'],
            ['кириллица.jpg'],
            ['/path/\1'],
            ['file.err'],
            ['../file.jpg'],
            ['.../i.jpg'],
            ['f../i.jpg'],
            ['/../file.jpg'],
            ['dir.name/f.jpeg'],
            ['/.dir/f.jpg'],
        ];
    }
    
    /**
     * @dataProvider filenamesProvider
     */
    public function testConstruct_Filename_ModifiedFilenameReturned(string $src, string $dst): void
    {
        $imageRequest = new ThumbnailKeyParser($this->factory, $src);
        $this->assertEquals($dst, $imageRequest->getExtractionRequest());
    }
    
    public function filenamesProvider(): array
    {
        return [
            ['./f.jpeg', '/f.jpeg'],
            ['/./f.jpeg', '/f.jpeg'],
            ['Img.jpg', '/Img.jpg'],
            ['/path/image.JPG', '/path/image.JPG'],
            ['/path/image_sz80x100.jpg', '/path/image.jpg'],
            ['_root/i_q95.jpeg', '/_root/i.jpeg'],
        ];
    }
    
    /**
     * @dataProvider filenamesWithTransformationsProvider
     */
    public function testConstruct_Filename_CountOfTransformationsReturned(string $filename, int $count): void
    {
        $imageRequest = new ThumbnailKeyParser($this->factory, $filename);
        $this->assertEquals($count, $imageRequest->getTransformations()->count());
    }
    
    public function filenamesWithTransformationsProvider(): array
    {
        return [
            ['i.jpg', 0],
            ['i_q95.jpg', 1],
            ['i_sz85_q7.jpg', 2],
        ];
    }
    
    public function testConstruct_FilenameWithQuality_QualityValueReturned(): void
    {
        $factory = new class implements TransformationsFactoryInterface {
            public function create(string $config): TransformationInterface
            {
                return new class extends Quality {
                    public function __construct() {}
                    public function getValue(): int
                    {
                        return 50;
                    }
                };
            }
        };
        
        $imageRequest = new ThumbnailKeyParser($factory, 'i_q.jpg');
        $this->assertEquals(50, $imageRequest->getQuality());
        
        $imageRequest = new ThumbnailKeyParser($factory, 'i.jpg');
        $this->assertNull($imageRequest->getQuality());
    }
}
