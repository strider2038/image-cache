<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;

use Strider2038\ImgCache\Imaging\{
    Image,
    ImageRequest
};
use Strider2038\ImgCache\Imaging\Transformation\{
    Quality,
    TransformationInterface,
    TransformationsFactoryInterface
};

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageRequestTest extends TestCase
{
    private $factory;
    
    public function setUp()
    {
        $this->factory = new class implements TransformationsFactoryInterface {
            public function create(string $config): TransformationInterface
            {
                return new class implements TransformationInterface {
                    public function apply(Image $image): void {}
                };
            }
        };
    }
    
    /**
     * @dataProvider incorrectFilenamesProvider
     * @expectedException Strider2038\ImgCache\Exception\InvalidImageException
     * @expectedExceptionCode 400
     */
    public function testConstruct_FilenameWithIllegalChars_ExceptionThrown(string $filename): void
    {
        new ImageRequest($this->factory, $filename);
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
        $imageRequest = new ImageRequest($this->factory, $src);
        $this->assertEquals($dst, $imageRequest->getFileName());
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
        $imageRequest = new ImageRequest($this->factory, $filename);
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
        
        $imageRequest = new ImageRequest($factory, 'i_q.jpg');
        $this->assertEquals(50, $imageRequest->getQuality());
        
        $imageRequest = new ImageRequest($factory, 'i.jpg');
        $this->assertNull($imageRequest->getQuality());
    }
}
