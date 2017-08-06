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

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class TransformationsCreator implements TransformationsCreatorInterface
{
    /** @var TransformationFactoryFlyweightInterface */
    private $factoryFlyweight;
    
    public function __construct(TransformationFactoryFlyweightInterface $factoryFlyweight)
    {
        $this->factoryFlyweight = $factoryFlyweight;
    }

    public function create(string $configuration): ?TransformationInterface
    {
        $factory = $this->factoryFlyweight->findFactory(substr($configuration, 0, 2));
        if ($factory !== null) {
            return $factory->create(substr($configuration, 2));
        }

        $factory = $this->factoryFlyweight->findFactory(substr($configuration, 0, 1));
        if ($factory !== null) {
            return $factory->create(substr($configuration, 1));
        }

        return null;
    }

}
