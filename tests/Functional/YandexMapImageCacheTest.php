<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Functional;

use Strider2038\ImgCache\Imaging\Image\ImageFile;
use Strider2038\ImgCache\Imaging\ImageCache;
use Strider2038\ImgCache\Tests\Support\FunctionalTestCase;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class YandexMapImageCacheTest extends FunctionalTestCase
{
    private const IMAGE_WITH_INVALID_PARAMETERS = '/size=0,0.jpg';
    private const IMAGE_NAME = '/ll=60.715799,28.729073_size=150,100.jpg';
    private const IMAGE_WEB_FILENAME = self::WEB_DIRECTORY . '/ll=60.715799,28.729073_size=150,100.jpg';

    /** @var ImageCache */
    private $cache;

    protected function setUp()
    {
        parent::setUp();
        $container = $this->loadContainer('yandex-map-image-cache.yml');
        $this->cache = $container->get('image_cache');
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     */
    public function get_givenNameWithInvalidParameters_exceptionThrown(): void
    {
        $this->cache->get(self::IMAGE_WITH_INVALID_PARAMETERS);
    }

    /** @test */
    public function get_givenNameWithValidParameters_imageIsCreated(): void
    {
        $image = $this->cache->get(self::IMAGE_NAME);

        $this->assertInstanceOf(ImageFile::class, $image);
        $this->assertFileExists(self::IMAGE_WEB_FILENAME);
    }
}
