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

use Strider2038\ImgCache\Imaging\Image\ImageParametersFactoryInterface;
use Strider2038\ImgCache\Imaging\Parsing\ImageParametersConfiguratorInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingConfiguration;
use Strider2038\ImgCache\Imaging\Processing\Transforming\TransformationCollection;
use Strider2038\ImgCache\Imaging\Processing\Transforming\TransformationCreatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailProcessingConfigurationParser implements ProcessingConfigurationParserInterface
{
    /** @var TransformationCreatorInterface */
    private $transformationsCreator;

    /** @var ImageParametersFactoryInterface */
    private $imageParametersFactory;

    /** @var ImageParametersConfiguratorInterface */
    private $imageParametersConfigurator;

    public function __construct(
        TransformationCreatorInterface $transformationsCreator,
        ImageParametersFactoryInterface $imageParametersFactory,
        ImageParametersConfiguratorInterface $imageParametersConfigurator
    ) {
        $this->transformationsCreator = $transformationsCreator;
        $this->imageParametersFactory = $imageParametersFactory;
        $this->imageParametersConfigurator = $imageParametersConfigurator;
    }

    public function parseConfiguration(string $configuration): ProcessingConfiguration
    {
        $transformations = new TransformationCollection();
        $imageParameters = $this->imageParametersFactory->createImageParameters();
        $configurationValues = array_filter(explode('_', $configuration));

        foreach ($configurationValues as $value) {
            $transformation = $this->transformationsCreator->findAndCreateTransformation($value);
            if ($transformation !== null) {
                $transformations->add($transformation);
            } else {
                $this->imageParametersConfigurator->updateParametersByConfiguration($imageParameters, $value);
            }
        }

        return new ProcessingConfiguration($transformations, $imageParameters);
    }
}
