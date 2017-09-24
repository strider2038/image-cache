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

class ThumbnailKeyTest extends TestCase
{
    private const PUBLIC_FILENAME = 'a.jpg';
    private const PROCESSING_CONFIGURATION = 'q5';

    /** @test */
    public function construct_givenProperties_propertiesAreSet(): void
    {
        $key = new ThumbnailKey(self::PUBLIC_FILENAME, self::PROCESSING_CONFIGURATION);

        $this->assertEquals(self::PUBLIC_FILENAME, $key->getPublicFilename());
        $this->assertEquals(self::PROCESSING_CONFIGURATION, $key->getProcessingConfiguration());
    }

    /**
     * @test
     * @param string $processingConfiguration
     * @param bool $expectedResult
     * @dataProvider processingConfigurationProvider
     */
    public function hasProcessingConfiguration_givenProcessingConfiguration_boolIsReturned(
        string $processingConfiguration,
        bool $expectedResult
    ): void {
        $key = new ThumbnailKey(self::PUBLIC_FILENAME, $processingConfiguration);

        $result = $key->hasProcessingConfiguration();

        $this->assertEquals($expectedResult, $result);
    }

    public function processingConfigurationProvider(): array
    {
        return [
            ['', false],
            ['a', true],
        ];
    }
}
