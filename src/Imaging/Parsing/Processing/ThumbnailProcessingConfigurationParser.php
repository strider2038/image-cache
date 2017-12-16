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
use Strider2038\ImgCache\Imaging\Processing\SaveOptionsFactoryInterface;
use Strider2038\ImgCache\Imaging\Transformation\TransformationCollection;
use Strider2038\ImgCache\Imaging\Transformation\TransformationCreatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailProcessingConfigurationParser implements ProcessingConfigurationParserInterface
{
    /** @var TransformationCreatorInterface */
    private $transformationsCreator;

    /** @var SaveOptionsFactoryInterface */
    private $saveOptionsFactory;

    /** @var SaveOptionsConfiguratorInterface */
    private $saveOptionsConfigurator;

    public function __construct(
        TransformationCreatorInterface $transformationsCreator,
        SaveOptionsFactoryInterface $saveOptionsFactory,
        SaveOptionsConfiguratorInterface $saveOptionsConfigurator
    ) {
        $this->transformationsCreator = $transformationsCreator;
        $this->saveOptionsFactory = $saveOptionsFactory;
        $this->saveOptionsConfigurator = $saveOptionsConfigurator;
    }

    public function parseConfiguration(string $configuration): ProcessingConfiguration
    {
        $transformations = new TransformationCollection();
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

                $this->saveOptionsConfigurator->updateSaveOptionsByConfiguration($saveOptions, $value);

            }
        }

        return new ProcessingConfiguration($transformations, $saveOptions, $isDefault);
    }
}
