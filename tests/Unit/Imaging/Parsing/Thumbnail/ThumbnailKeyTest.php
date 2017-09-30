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
use Strider2038\ImgCache\Imaging\Processing\ProcessingConfigurationInterface;
use Strider2038\ImgCache\Tests\Support\Phake\ProviderTrait;

class ThumbnailKeyTest extends TestCase
{
    use ProviderTrait;

    private const PUBLIC_FILENAME = 'a.jpg';

    /** @var ProcessingConfigurationInterface */
    private $processingConfiguration;

    protected function setUp()
    {
        $this->processingConfiguration = \Phake::mock(ProcessingConfigurationInterface::class);
    }

    /** @test */
    public function construct_givenProperties_propertiesAreSet(): void
    {
        $key = new ThumbnailKey(self::PUBLIC_FILENAME, $this->processingConfiguration);

        $this->assertEquals(self::PUBLIC_FILENAME, $key->getPublicFilename());
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
        $key = new ThumbnailKey(self::PUBLIC_FILENAME, $this->processingConfiguration);
        \Phake::when($this->processingConfiguration)->isDefault()->thenReturn($isDefault);

        $result = $key->hasProcessingConfiguration();

        $this->assertEquals(!$isDefault, $result);
    }
}
