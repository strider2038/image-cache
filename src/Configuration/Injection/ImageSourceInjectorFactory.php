<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Configuration\Injection;

use Strider2038\ImgCache\Configuration\ImageSource\AbstractImageSource;
use Strider2038\ImgCache\Configuration\ImageSource\FilesystemImageSource;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageSourceInjectorFactory implements ImageSourceInjectorFactoryInterface
{
    private const INJECTORS_MAP = [
        FilesystemImageSource::class => FilesystemImageSourceInjector::class,
    ];

    public function createSettingsInjectorForImageSource(AbstractImageSource $imageSource): SettingsInjectorInterface
    {
        $sourceClassName = \get_class($imageSource);
        $injectorClassName = self::INJECTORS_MAP[$sourceClassName];

        return $this->createInjectorClassForImageSource($injectorClassName, $imageSource);
    }

    private function createInjectorClassForImageSource($injectorClassName, AbstractImageSource $imageSource)
    {
        $reflector = new \ReflectionClass($injectorClassName);

        return $reflector->newInstanceArgs([$imageSource]);
    }
}
