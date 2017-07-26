<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Extraction\Request;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Extraction\Request\FileExtractionRequestInterface;
use Strider2038\ImgCache\Imaging\Extraction\Request\ThumbnailRequestConfiguration;
use Strider2038\ImgCache\Imaging\Transformation\TransformationInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationsCollection;

class ThumbnailRequestConfigurationTest extends TestCase
{
    /** @var FileExtractionRequestInterface */
    private $extractionRequest;

    protected function setUp()
    {
        $this->extractionRequest = \Phake::mock(FileExtractionRequestInterface::class);
    }

    public function testGetExtractionRequest_GivenFileExtractionRequest_FileExtractionRequestIsReturned(): void
    {
        $requestConfiguration = $this->createRequestConfiguration();

        $extractionRequest = $requestConfiguration->getExtractionRequest();

        $this->assertInstanceOf(FileExtractionRequestInterface::class, $extractionRequest);
    }

    public function testHasTransformations_TransformationsIsNotSet_FalseIsReturned(): void
    {
        $requestConfiguration = $this->createRequestConfiguration();

        $hasTransformations = $requestConfiguration->hasTransformations();

        $this->assertFalse($hasTransformations);
    }

    /**
     * @param TransformationsCollection $collection
     * @param bool $expectedHasTransformations
     * @dataProvider hasTransformationsProvider
     */
    public function testHasTransformations_TransformationsIsSet_BoolIsReturned(
        TransformationsCollection $collection,
        bool $expectedHasTransformations
    ): void {
        $requestConfiguration = $this->createRequestConfiguration();
        $requestConfiguration->setTransformations($collection);

        $hasTransformations = $requestConfiguration->hasTransformations();

        $this->assertEquals($expectedHasTransformations, $hasTransformations);
    }

    public function hasTransformationsProvider(): array
    {
        return [
            [$this->givenTransformationsCount(0), false],
            [$this->givenTransformationsCount(1), true],
            [$this->givenTransformationsCount(2), true],
        ];
    }

    private function createRequestConfiguration(): ThumbnailRequestConfiguration
    {
        return new ThumbnailRequestConfiguration($this->extractionRequest);
    }

    private function givenTransformationsCount(int $count = 0): TransformationsCollection
    {
        $collection = new TransformationsCollection();

        for ($i = 0; $i < $count; $i++) {
            $collection->add(\Phake::mock(TransformationInterface::class));
        }

        return $collection;
    }
}
