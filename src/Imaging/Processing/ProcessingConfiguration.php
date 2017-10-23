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

use Strider2038\ImgCache\Imaging\Transformation\TransformationsCollection;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ProcessingConfiguration
{
    /** @var TransformationsCollection */
    private $transformations;

    /** @var SaveOptions */
    private $saveOptions;

    /** @var bool */
    private $isDefault;

    public function __construct(
        TransformationsCollection $transformations,
        SaveOptions $saveOptions,
        bool $isDefault
    ) {
        $this->transformations = $transformations;
        $this->saveOptions = $saveOptions;
        $this->isDefault = $isDefault;
    }

    public function getTransformations(): TransformationsCollection
    {
        return $this->transformations;
    }

    public function getSaveOptions(): SaveOptions
    {
        return $this->saveOptions;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }
}