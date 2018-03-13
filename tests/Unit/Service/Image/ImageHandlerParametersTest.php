<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Service\Image;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Configuration\ImageSource\AbstractImageSource;
use Strider2038\ImgCache\Enum\HttpMethodEnum;
use Strider2038\ImgCache\Service\Image\ImageHandlerParameters;

class ImageHandlerParametersTest extends TestCase
{
    /** @test */
    public function construct_givenParameters_parametersSetAndAccessible(): void
    {
        $method = new HttpMethodEnum(HttpMethodEnum::GET);
        $source = \Phake::mock(AbstractImageSource::class);

        $parameters = new ImageHandlerParameters($method, $source);

        $this->assertEquals($method, $parameters->getHttpMethod());
        $this->assertEquals($source, $parameters->getImageSource());
    }
}
