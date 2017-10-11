<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Parsing\Yandex\Map;

use Strider2038\ImgCache\Exception\InvalidRequestValueException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ValueConfiguratorFactory implements ValueConfiguratorFactoryInterface
{
    private const CLASS_MAP = [
        'l' => LayersConfigurator::class,
        'll' => LongitudeAndLatitudeConfigurator::class,
        'z' => ZoomConfigurator::class,
        'size' => WidthAndHeightConfigurator::class,
        'scale' => ScaleConfigurator::class
    ];

    public function create(string $name): ValueConfiguratorInterface
    {
        if (!array_key_exists($name, self::CLASS_MAP)) {
            throw new InvalidRequestValueException(sprintf('Unknown parameter name "%s"', $name));
        }

        $class = self::CLASS_MAP[$name];
        return new $class;
    }
}
