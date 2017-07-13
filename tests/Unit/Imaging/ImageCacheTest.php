<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Imaging;

use Strider2038\ImgCache\Imaging\{
    Image,
    ImageCache
};
use Strider2038\ImgCache\Imaging\Transformation\{
    TransformationsFactoryInterface,
    TransformationInterface
};
use Strider2038\ImgCache\Imaging\Processing\{
    ProcessingEngineInterface,
    ProcessingImageInterface
};
use Strider2038\ImgCache\Imaging\Source\SourceInterface;
use Strider2038\ImgCache\Tests\Support\FileTestCase;

class ImageCacheTest extends FileTestCase
{


    /** @var SourceInterface */
    private $source;

    /** @var TransformationsFactoryInterface */
    private $transformationsFactory;

    /** @var ProcessingEngineInterface */
    private $processingEngineInterface;

    protected function setUp()
    {
        $this->source = new class implements SourceInterface {
            public $testGetReturning;
            public function get(string $filename): ?Image
            {
                return $this->testGetReturning;
            }
        };

        $this->transformationsFactory = new class implements TransformationsFactoryInterface {
            public function create(string $config): TransformationInterface
            {

            }
        };

        $this->processingEngineInterface = new class implements ProcessingEngineInterface {
            public function open(string $filename): ProcessingImageInterface
            {

            }
        };
    }

    public function testGet_ImageDoesNotExist_NullIsReturned(): void
    {
        $cache = new ImageCache(
            self::TEST_CACHE_DIR,
            $this->source,
            $this->transformationsFactory,
            $this->processingEngineInterface
        );
        $this->source->testGetReturning = null;

        $image = $cache->get('a.jpg');

        $this->assertNull($image);
    }
}
