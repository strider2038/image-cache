<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Image;

use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Exception\InvalidImageException;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageFactory implements ImageFactoryInterface
{
    /** @var ImageParametersFactoryInterface */
    private $parametersFactory;

    /** @var EntityValidatorInterface */
    private $validator;

    public function __construct(
        ImageParametersFactoryInterface $imageParametersFactory,
        EntityValidatorInterface $imageValidator
    ) {
        $this->parametersFactory = $imageParametersFactory;
        $this->validator = $imageValidator;
    }

    public function createImageFromStream(StreamInterface $stream, ImageParameters $parameters = null): Image
    {
        $image = new Image(
            $stream,
            $parameters ?? $this->parametersFactory->createImageParameters()
        );

        $this->validator->validateWithException($image, InvalidImageException::class);

        return $image;
    }
}
