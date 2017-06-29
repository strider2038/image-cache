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

use Strider2038\ImgCache\Exception\InvalidConfigException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class TransformationsFactory implements TransformationsFactoryInterface
{
    /** @var TransformationBuilderInterface[] */
    private $builders = [];
    
    /** @var string[] */
    private $buildersMap = [];
    
    public function __construct(array $buildersMap = null)
    {
        if ($buildersMap === null) {
            $this->buildersMap = self::getDefaultBuildersMap();
            return;
        }
        if (count($buildersMap) <= 0) {
            throw new InvalidConfigException('Builders map cannot be empty');
        }
        foreach ($buildersMap as $class) {
            if (!class_exists($class)) {
                throw new InvalidConfigException(
                    "Class with name '{$class}' does not exist"
                );
            }
            $implements = class_implements($class);
            if (!isset($implements[TransformationBuilderInterface::class])) {
                throw new InvalidConfigException(
                    "Class '{$class}' must implement " . TransformationBuilderInterface::class
                );
            }
        }
        $this->buildersMap = $buildersMap;
    }
    
    public function create(string $config): TransformationInterface
    {
        $builder = $this->getBuilder(substr($config, 0, 2));
        if ($builder !== null) {
            return $builder->build(substr($config, 2));
        }
        $builder = $this->getBuilder(substr($config, 0, 1));
        if ($builder !== null) {
            return $builder->build(substr($config, 1));
        }
        throw new InvalidConfigException("Cannot create transformation for '{$config}'");
    }
    
    public function getBuilder(string $index): ?TransformationBuilderInterface
    {
        if (isset($this->builders[$index])) {
            return $this->builders[$index];
        }
        if (isset($this->buildersMap[$index])) {
            return $this->builders[$index] = new $this->buildersMap[$index];
        }
        return null;
    }
    
    public static function getDefaultBuildersMap(): array
    {
        return [
            'q' => QualityBuilder::class,
            's' => ResizeBuilder::class,
        ];
    }
}
