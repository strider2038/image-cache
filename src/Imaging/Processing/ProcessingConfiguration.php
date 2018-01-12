<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Processing;

use Strider2038\ImgCache\Imaging\Image\ImageParameters;
use Strider2038\ImgCache\Imaging\Processing\Transforming\TransformationCollection;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ProcessingConfiguration
{
    /** @var TransformationCollection */
    private $transformations;

    /** @var ImageParameters */
    private $imageParameters;

    public function __construct(TransformationCollection $transformations, ImageParameters $imageParameters)
    {
        $this->transformations = $transformations;
        $this->imageParameters = $imageParameters;
    }

    public function getTransformations(): TransformationCollection
    {
        return $this->transformations;
    }

    public function getImageParameters(): ImageParameters
    {
        return $this->imageParameters;
    }
}
