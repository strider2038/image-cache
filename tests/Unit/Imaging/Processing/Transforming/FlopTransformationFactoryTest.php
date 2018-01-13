<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Processing\Transforming;

use Strider2038\ImgCache\Imaging\Processing\Transforming\FlopTransformation;
use Strider2038\ImgCache\Imaging\Processing\Transforming\FlopTransformationFactory;
use PHPUnit\Framework\TestCase;

class FlopTransformationFactoryTest extends TestCase
{
    /** @test */
    public function createTransformation_givenNoParameters_flipTransformationCreatedAndReturned(): void
    {
        $factory = new FlopTransformationFactory();

        $transformation = $factory->createTransformation('');

        $this->assertInstanceOf(FlopTransformation::class, $transformation);
    }
}
