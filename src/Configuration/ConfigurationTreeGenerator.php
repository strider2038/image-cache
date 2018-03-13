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
class ConfigurationTreeGenerator implements ConfigurationInterface
{
    private const DEFAULT_CACHED_IMAGE_QUALITY = 85;

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('application');

        $rootNode
            ->children()
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
                            ->scalarNode('cache_directory')
                                ->isRequired()
                                ->cannotBeEmpty()
                                ->validate()
                                    ->ifTrue(function($value) {
                                        return !ConfigurationValidator::isValidDirectoryName($value);
                                    })
                                    ->thenInvalid(ConfigurationValidator::INVALID_DIRECTORY_NAME_MESSAGE)
                                ->end()
                                ->validate()
                                    ->always()
                                    ->then(function($value) {
                                        if ($value !== '/') {
                                            $value = sprintf('/%s/', trim($value, '/'));
                                        }

                                        return $value;
                                    })
                                ->end()
                            ->end()
                            ->scalarNode('storage_directory')
                                ->defaultValue('/')
                                ->validate()
                                    ->ifTrue(function(string $value) {
                                        return !ConfigurationValidator::isValidDirectoryName($value);
                                    })
                                    ->thenInvalid(ConfigurationValidator::INVALID_DIRECTORY_NAME_MESSAGE)
                                ->end()
                                ->validate()
                                    ->always()
                                    ->then(function(string $value) {
                                        if ($value !== '/') {
                                            $value = sprintf('/%s/', trim($value, '/'));
                                        }

                                        return $value;
                                    })
                                ->end()
                            ->end()
                            ->enumNode('processor_type')
                                ->values(['copy', 'thumbnail'])
                                ->defaultValue('thumbnail')
                            ->end()
                            ->enumNode('driver')
                                ->values(['yandex'])
                                ->defaultValue('yandex')
                            ->end()
                            ->scalarNode('driver_uri')
                                ->defaultValue('')
                            ->end()
                            ->scalarNode('oauth_token')
                                ->defaultValue('')
                            ->end()
                            ->scalarNode('api_key')
                                ->defaultValue('')
                            ->end()
                        ->end()
                        ->validate()
                            ->ifTrue(function(array $imageSource) {
                                return (
                                    $imageSource['type'] === 'webdav'
                                    && (
                                        $imageSource['driver_uri'] === ''
                                        || $imageSource['oauth_token'] === ''
                                    )
                                );
                            })
                            ->thenInvalid(
                                'Keys "driver_uri" and "oauth_token" cannot be empty for source type "geomap".'
                            )
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
