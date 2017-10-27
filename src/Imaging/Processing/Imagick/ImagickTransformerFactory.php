<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Processing\Imagick;

use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Processing\ImageTransformerFactoryInterface;
use Strider2038\ImgCache\Imaging\Processing\ImageTransformerInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImagickTransformerFactory implements ImageTransformerFactoryInterface
{
    /** @var ImageFactoryInterface */
    private $imageFactory;

    public function __construct(ImageFactoryInterface $imageFactory)
    {
        $this->imageFactory = $imageFactory;
    }

    public function createTransformerForImage(Image $image): ImageTransformerInterface
    {
        $imagick = new \Imagick();
        $imagick->readImageBlob($image->getData()->getContents());

        return new ImagickTransformer($imagick, $this->imageFactory);
    }
}
