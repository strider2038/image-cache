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

use Strider2038\ImgCache\Core\Component;
use Strider2038\ImgCache\Exception\InvalidConfigException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class TransformationsFactory extends Component implements TransformationsFactoryInterface
{
    /** @var array */
    private $builders = [];
    
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
        $map = $this->getBuildersMap();
        if (isset($map[$index])) {
            return $this->builders[$index] = new $map[$index];
        }
        return null;
    }
    
    public function getBuildersMap(): array
    {
        return [
            'q' => QualityBuilder::class,
            's' => ResizeBuilder::class,
        ];
    }
}
