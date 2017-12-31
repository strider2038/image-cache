<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Service\Routing;

use Strider2038\ImgCache\Exception\InvalidConfigurationException;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class RoutingPathFactory implements RoutingPathFactoryInterface
{
    /** @var EntityValidatorInterface */
    private $validator;

    public function __construct(EntityValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function createRoutingPath(string $urlPrefix, string $controllerId): RoutingPath
    {
        $path = new RoutingPath($urlPrefix, $controllerId);
        $this->validator->validateWithException($path, InvalidConfigurationException::class);

        return $path;
    }

    public function createRoutingPathCollection(array $routingMap): RoutingPathCollection
    {
        $map = new RoutingPathCollection();

        foreach ($routingMap as $urlPrefix => $controllerId) {
            $map->add($this->createRoutingPath($urlPrefix, $controllerId));
        }

        return $map;
    }
}
