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
use Strider2038\ImgCache\Imaging\Processing\ProcessingConfiguration;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;
use Strider2038\ImgCache\Imaging\Transformation\TransformationsCollection;

class ProcessingConfigurationTest extends TestCase
{
    /** @var TransformationsCollection */
    private $transformations;

    /** @var SaveOptions */
    private $saveOptions;

    protected function setUp()
    {
        $this->transformations = \Phake::mock(TransformationsCollection::class);
        $this->saveOptions = \Phake::mock(SaveOptions::class);
    }

    public function testConstruct_GivenProperties_PropertiesAreSet(): void
    {
        $configuration = new ProcessingConfiguration($this->transformations, $this->saveOptions);

        $this->assertSame($this->transformations, $configuration->getTransformations());
        $this->assertSame($this->saveOptions, $configuration->getSaveOptions());
    }
}
