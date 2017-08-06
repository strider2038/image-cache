<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Transformation;

use Strider2038\ImgCache\Exception\InvalidConfigurationException;


/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class TransformationFactoryFlyweight implements TransformationFactoryFlyweightInterface
{
    /** @var TransformationFactoryInterface[] */
    private $factories;

    /** @var string[] */
    private $factoriesMap;

    public function __construct(array $factoriesMap = [])
    {
        if (count($factoriesMap) <= 0) {
            $this->factoriesMap = $this->getDefaultFactoriesMap();
            return;
        }

        foreach ($factoriesMap as $class) {
            if (!class_exists($class)) {
                throw new InvalidConfigurationException(
                    "Class with name '{$class}' does not exist"
                );
            }
            $implements = class_implements($class);
            if (!isset($implements[TransformationFactoryInterface::class])) {
                throw new InvalidConfigurationException(
                    "Class '{$class}' must implement " . TransformationFactoryInterface::class
                );
            }
        }

        $this->factoriesMap = $factoriesMap;
    }

    public function getDefaultFactoriesMap(): array
    {
        return [
            's' => ResizeFactory::class,
        ];
    }

    public function findFactory(string $index): ?TransformationFactoryInterface
    {
        if (!isset($this->factoriesMap[$index])) {
            return null;
        }

        if (!isset($this->factories[$index])) {
            $this->factories[$index] = new $this->factoriesMap[$index];
        }

        return $this->factories[$index];
    }
}