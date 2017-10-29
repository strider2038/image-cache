<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Parsing\Thumbnail;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Parsing\Thumbnail\ThumbnailKey;
use Strider2038\ImgCache\Imaging\Processing\ProcessingConfiguration;
use Strider2038\ImgCache\Tests\Support\Phake\ProviderTrait;

class ThumbnailKeyTest extends TestCase
{
    use ProviderTrait;

    private const PUBLIC_FILENAME = 'a.jpg';
    private const THUMBNAIL_MASK = 'thumbnail_mask';

    /** @var ProcessingConfiguration */
    private $processingConfiguration;

    protected function setUp()
    {
        $this->processingConfiguration = \Phake::mock(ProcessingConfiguration::class);
    }

    /** @test */
    public function construct_givenProperties_propertiesAreSet(): void
    {
        $key = $this->createThumbnailKey();

        $this->assertEquals(self::PUBLIC_FILENAME, $key->getPublicFilename());
        $this->assertEquals(self::THUMBNAIL_MASK, $key->getThumbnailMask());
        $this->assertSame($this->processingConfiguration, $key->getProcessingConfiguration());
    }

    /**
     * @test
     * @param bool $isDefault
     * @dataProvider boolValuesProvider
     */
    public function hasProcessingConfiguration_givenProcessingConfiguration_boolIsReturned(
        bool $isDefault
    ): void {
        $key = $this->createThumbnailKey();
        \Phake::when($this->processingConfiguration)->isDefault()->thenReturn($isDefault);

        $result = $key->hasProcessingConfiguration();

        $this->assertEquals(!$isDefault, $result);
    }

    private function createThumbnailKey(): ThumbnailKey
    {
        return new ThumbnailKey(
            self::PUBLIC_FILENAME,
            self::THUMBNAIL_MASK,
            $this->processingConfiguration
        );
    }
}
