<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Parsing\Processing;

use Strider2038\ImgCache\Imaging\Parsing\SaveOptionsConfiguratorInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingConfiguration;
use Strider2038\ImgCache\Imaging\Processing\ProcessingConfigurationInterface;
use Strider2038\ImgCache\Imaging\Processing\SaveOptionsFactoryInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationsCollection;
use Strider2038\ImgCache\Imaging\Transformation\TransformationsCreatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailProcessingConfigurationParser implements ProcessingConfigurationParserInterface
{
    /** @var TransformationsCreatorInterface */
    private $transformationsCreator;

    /** @var SaveOptionsFactoryInterface */
    private $saveOptionsFactory;

    /** @var SaveOptionsConfiguratorInterface */
    private $saveOptionsConfigurator;

    public function __construct(
        TransformationsCreatorInterface $transformationsCreator,
        SaveOptionsFactoryInterface $saveOptionsFactory,
        SaveOptionsConfiguratorInterface $saveOptionsConfigurator
    ) {
        $this->transformationsCreator = $transformationsCreator;
        $this->saveOptionsFactory = $saveOptionsFactory;
        $this->saveOptionsConfigurator = $saveOptionsConfigurator;
    }

    public function parse(string $configuration): ProcessingConfiguration
    {
        $transformations = new TransformationsCollection();
        $saveOptions = $this->saveOptionsFactory->create();
        $isDefault = true;

        if (!empty($configuration)) {
            $configurationValues = explode('_', $configuration);
            $isDefault = false;

            foreach ($configurationValues as $value) {

                $transformation = $this->transformationsCreator->create($value);
                if ($transformation !== null) {
                    $transformations->add($transformation);
                    continue;
                }

                $this->saveOptionsConfigurator->configure($saveOptions, $value);

            }
        }

        return new ProcessingConfiguration($transformations, $saveOptions, $isDefault);
    }
}
