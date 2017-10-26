<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Transformation;

use Strider2038\ImgCache\Enum\ResizeModeEnum;
use Strider2038\ImgCache\Imaging\Processing\ImageTransformerInterface;
use Strider2038\ImgCache\Imaging\Processing\Rectangle;
use Strider2038\ImgCache\Imaging\Processing\Size;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ResizeTransformation implements TransformationInterface
{
    /** @var ResizeParameters */
    private $parameters;

    public function __construct(ResizeParameters $parameters)
    {
        $this->parameters = $parameters;
    }

    public function getParameters(): ResizeParameters
    {
        return $this->parameters;
    }

    public function apply(ImageTransformerInterface $transformer): void
    {
        $sourceSize = $transformer->getSize();

        $ratios = [
            (float) $this->parameters->getWidth() / (float) $sourceSize->getWidth(),
            (float) $this->parameters->getHeight() / (float) $sourceSize->getHeight(),
        ];
        
        $ratio = 1;
        switch ($this->parameters->getMode()->getValue()) {
            case ResizeModeEnum::FIT_IN: $ratio = min($ratios); break;
            case ResizeModeEnum::STRETCH: $ratio = max($ratios); break;
            case ResizeModeEnum::PRESERVE_WIDTH: $ratio = $ratios[0]; break;
            case ResizeModeEnum::PRESERVE_HEIGHT: $ratio = $ratios[1]; break;
        }

        $newSize = new Size(
            round($sourceSize->getWidth() * $ratio),
            round($sourceSize->getHeight() * $ratio)
        );
        $transformer->resize($newSize);
        
        if ($this->parameters->getMode()->getValue() === ResizeModeEnum::STRETCH) {
            $cropRectangle = new Rectangle(
                $this->parameters->getWidth(),
                $this->parameters->getHeight(),
                max(0, round(($newSize->getWidth() - $this->parameters->getWidth()) / 2)),
                max(0, round(($newSize->getHeight() - $this->parameters->getHeight()) / 2))
            );
            $transformer->crop($cropRectangle);
        }
    }
}
