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
use Strider2038\ImgCache\Imaging\Processing\ImageTransformerInterface;
use Strider2038\ImgCache\Imaging\Processing\RectangleInterface;
use Strider2038\ImgCache\Imaging\Processing\Size;
use Strider2038\ImgCache\Imaging\Processing\SizeInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImagickTransformer implements ImageTransformerInterface
{
    /** @var \Imagick */
    private $imagick;

    /** @var ImageFactoryInterface */
    private $imageFactory;

    public function __construct(\Imagick $imagick, ImageFactoryInterface $imageFactory)
    {
        $this->imagick = $imagick;
        $this->imageFactory = $imageFactory;
    }

    public function resize(SizeInterface $size): ImageTransformerInterface
    {
        $this->imagick->resizeImage($size->getWidth(), $size->getHeight(), \Imagick::FILTER_LANCZOS, 1);

        return $this;
    }

    public function crop(RectangleInterface $rectangle): ImageTransformerInterface
    {
        $this->imagick->cropImage(
            $rectangle->getWidth(),
            $rectangle->getHeight(),
            $rectangle->getLeft(),
            $rectangle->getTop()
        );

        return $this;
    }

    public function getSize(): SizeInterface
    {
        return new Size($this->imagick->getImageWidth(), $this->imagick->getImageHeight());
    }

    public function getImage(): Image
    {
        $data = $this->imagick->getImageBlob();
        return $this->imageFactory->createFromData($data);
    }
}
