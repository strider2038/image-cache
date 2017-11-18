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
use Strider2038\ImgCache\Imaging\Validation\ModelValidatorInterface;
use Strider2038\ImgCache\Imaging\Validation\ViolationsFormatterInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class RoutingPathFactory implements RoutingPathFactoryInterface
{
    /** @var ModelValidatorInterface */
    private $validator;

    /** @var ViolationsFormatterInterface */
    private $violationsFormatter;

    public function __construct(ModelValidatorInterface $validator, ViolationsFormatterInterface $violationsFormatter)
    {
        $this->validator = $validator;
        $this->violationsFormatter = $violationsFormatter;
    }

    public function createRoutingPath(string $urlPrefix, string $controllerId): RoutingPath
    {
        $path = new RoutingPath($urlPrefix, $controllerId);
        $violations = $this->validator->validate($path);

        if (count($violations) > 0) {
            throw new InvalidConfigurationException(sprintf(
                'Invalid routing map: %s',
                $this->violationsFormatter->format($violations)
            ));
        }

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
