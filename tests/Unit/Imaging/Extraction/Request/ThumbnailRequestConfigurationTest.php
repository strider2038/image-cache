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
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;
use Strider2038\ImgCache\Imaging\Transformation\TransformationsCollection;

class ThumbnailRequestConfigurationTest extends TestCase
{
    /** @var FileExtractionRequestInterface */
    private $extractionRequest;

    /** @var TransformationsCollection */
    private $transformations;

    /** @var SaveOptions */
    private $saveOptions;

    protected function setUp()
    {
        $this->extractionRequest = \Phake::mock(FileExtractionRequestInterface::class);
        $this->transformations = \Phake::mock(TransformationsCollection::class);
        $this->saveOptions = \Phake::mock(SaveOptions::class);
    }

    public function testConstruct_Nop_AllInjectedClassesAreAvailable(): void
    {
        $requestConfiguration = $this->createRequestConfiguration();

        $extractionRequest = $requestConfiguration->getExtractionRequest();
        $transformations = $requestConfiguration->getTransformations();
        $saveOptions = $requestConfiguration->getSaveOptions();

        $this->assertInstanceOf(FileExtractionRequestInterface::class, $extractionRequest);
        $this->assertInstanceOf(TransformationsCollection::class, $transformations);
        $this->assertInstanceOf(SaveOptions::class, $saveOptions);
    }

    /**
     * @dataProvider hasTransformationsProvider
     */
    public function testHasTransformations_GivenTransformationsCount_TrueIsReturned(
        int $transformationsCount,
        bool $expectedHasTransformations
    ): void {
        $requestConfiguration = $this->createRequestConfiguration();
        $this->givenTransformationsCount($transformationsCount);

        $hasTransformations = $requestConfiguration->hasTransformations();

        $this->assertEquals($expectedHasTransformations, $hasTransformations);
    }

    public function hasTransformationsProvider(): array
    {
        return [
            [0, false],
            [1, true],
            [2, true],
        ];
    }

    private function createRequestConfiguration(): ThumbnailRequestConfiguration
    {
        return new ThumbnailRequestConfiguration(
            $this->extractionRequest,
            $this->transformations,
            $this->saveOptions
        );
    }

    private function givenTransformationsCount(int $transformationsCount): void
    {
        \Phake::when($this->transformations)
            ->count()
            ->thenReturn($transformationsCount);
    }
}
