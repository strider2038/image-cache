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

use Strider2038\ImgCache\Core\FileOperationsInterface;
use Strider2038\ImgCache\Core\Streaming\StreamFactoryInterface;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Imaging\Processing\ImageTransformerInterface;
use Strider2038\ImgCache\Imaging\Processing\PointInterface;
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

    /** @var FileOperationsInterface */
    private $fileOperations;

    /** @var StreamFactoryInterface */
    private $streamFactory;

    public function __construct(
        \Imagick $imagick,
        FileOperationsInterface $fileOperations,
        StreamFactoryInterface $streamFactory
    ) {
        $this->imagick = $imagick;
        $this->fileOperations = $fileOperations;
        $this->streamFactory = $streamFactory;
    }

    public function getImagick(): \Imagick
    {
        return $this->imagick;
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

    public function flip(): ImageTransformerInterface
    {
        $this->imagick->flipImage();

        return $this;
    }

    public function flop(): ImageTransformerInterface
    {
        $this->imagick->flopImage();

        return $this;
    }

    public function rotate(float $degree): ImageTransformerInterface
    {
        $this->imagick->rotateImage(new \ImagickPixel('#00000000'), $degree);

        return $this;
    }

    public function shift(PointInterface $point): ImageTransformerInterface
    {
        $transparentImage = new \Imagick();
        $transparentImage->newImage(
            $this->imagick->getImageWidth(),
            $this->imagick->getImageHeight(),
            new \ImagickPixel('#00000000')
        );

        $transparentImage->compositeImage(
            $this->imagick,
            \Imagick::COMPOSITE_COPY,
            $point->getX(),
            $point->getY()
        );

        $this->imagick = $transparentImage;

        return $this;
    }

    public function getSize(): SizeInterface
    {
        return new Size($this->imagick->getImageWidth(), $this->imagick->getImageHeight());
    }

    public function getData(): StreamInterface
    {
        $data = $this->imagick->getImageBlob();

        return $this->streamFactory->createStreamFromData($data);
    }

    public function setCompressionQuality(int $quality): ImageTransformerInterface
    {
        $this->imagick->setCompressionQuality($quality);

        return $this;
    }

    public function writeToFile(string $filename): ImageTransformerInterface
    {
        $this->fileOperations->createDirectory(dirname($filename));
        $this->imagick->writeImage($filename);

        return $this;
    }
}
