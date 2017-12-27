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
use Strider2038\ImgCache\Imaging\Processing\ProcessingConfiguration;
use Strider2038\ImgCache\Tests\Support\Phake\ProviderTrait;

class ThumbnailFilenameTest extends TestCase
{
    use ProviderTrait;

    private const VALUE = 'a.jpg';
    private const MASK = 'thumbnail_mask';

    /** @var ProcessingConfiguration */
    private $processingConfiguration;

    protected function setUp(): void
    {
        $this->processingConfiguration = \Phake::mock(ProcessingConfiguration::class);
    }

    /** @test */
    public function construct_givenProperties_propertiesAreSet(): void
    {
        $key = $this->createThumbnailKey();

        $this->assertEquals(self::VALUE, $key->getValue());
        $this->assertEquals(self::MASK, $key->getMask());
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

    private function createThumbnailKey(): ThumbnailFilename
    {
        return new ThumbnailFilename(
            self::VALUE,
            self::MASK,
            $this->processingConfiguration
        );
    }
}
