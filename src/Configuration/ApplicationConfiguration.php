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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ApplicationConfiguration implements ConfigurationInterface
{
    private const DEFAULT_CACHED_IMAGE_QUALITY = 85;

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('application');

        $rootNode->children()
            ->scalarNode('access_control_token')
                ->defaultValue('')
            ->end()
            ->integerNode('cached_image_quality')
                ->min(15)
                ->max(100)
                ->defaultValue(self::DEFAULT_CACHED_IMAGE_QUALITY)
            ->end()
            ->arrayNode('image_sources')
                ->isRequired()
                ->requiresAtLeastOneElement()
                ->arrayPrototype()
                    ->children()
                        ->enumNode('type')
                            ->isRequired()
                            ->values(['filesystem', 'webdav', 'geomap'])
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
