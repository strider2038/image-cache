<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Support;

use Psr\Container\ContainerInterface;
use Strider2038\ImgCache\Core\ApplicationParameters;
use Strider2038\ImgCache\Core\Service\ServiceContainerLoaderInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ServiceContainerLoaderFake implements ServiceContainerLoaderInterface
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function loadServiceContainerWithApplicationParameters(ApplicationParameters $parameters): ContainerInterface
    {
        return $this->container;
    }
}
