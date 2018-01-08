<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Processing;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Image\ImageParameters;
use Strider2038\ImgCache\Imaging\Processing\ProcessingConfiguration;
use Strider2038\ImgCache\Imaging\Transformation\TransformationCollection;
use Strider2038\ImgCache\Tests\Support\Phake\ProviderTrait;

class ProcessingConfigurationTest extends TestCase
{
    /** @var TransformationCollection */
    private $transformations;

    /** @var ImageParameters */
    private $imageParameters;

    protected function setUp(): void
    {
        $this->transformations = \Phake::mock(TransformationCollection::class);
        $this->imageParameters = \Phake::mock(ImageParameters::class);
    }

    /** @test */
    public function construct_givenProperties_propertiesAreSet(): void
    {
        $configuration = new ProcessingConfiguration($this->transformations, $this->imageParameters);

        $this->assertSame($this->transformations, $configuration->getTransformations());
        $this->assertSame($this->imageParameters, $configuration->getImageParameters());
    }
}
