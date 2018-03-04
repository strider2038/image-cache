<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Configuration;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 * @deprecated
 */
interface ContainerConfiguratorInterface
{
    public function updateContainerByConfiguration(ContainerInterface $container, Configuration $configuration): void;
}