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
use Strider2038\ImgCache\Imaging\Parsing\ImageParametersModifierInterface;
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

    /** @var ImageParametersModifierInterface */
    private $imageParametersModifier;

    public function __construct(
        TransformationCreatorInterface $transformationsCreator,
        ImageParametersFactoryInterface $imageParametersFactory,
        ImageParametersModifierInterface $imageParametersModifier
    ) {
        $this->transformationsCreator = $transformationsCreator;
        $this->imageParametersFactory = $imageParametersFactory;
        $this->imageParametersModifier = $imageParametersModifier;
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
                $this->imageParametersModifier->updateParametersByConfiguration($imageParameters, $value);
            }
        }

        return new ProcessingConfiguration($transformations, $imageParameters);
    }
}
