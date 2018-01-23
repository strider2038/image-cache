<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Configuration\Injection;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Configuration\ImageSource\FilesystemImageSource;
use Strider2038\ImgCache\Configuration\Injection\FilesystemImageSourceInjector;
use Strider2038\ImgCache\Configuration\Injection\ImageSourceInjectorFactory;

class ImageSourceInjectorFactoryTest extends TestCase
{
    /** @test */
    public function createSettingsInjectorForImageSource_givenImageSource_settingsInjectorForSpecifiedSourceCreatedAndReturned(): void
    {
        $factory = new ImageSourceInjectorFactory();
        $imageSource = new FilesystemImageSource('', '', '');

        $injector = $factory->createSettingsInjectorForImageSource($imageSource);

        $this->assertInstanceOf(FilesystemImageSourceInjector::class, $injector);
    }
}
