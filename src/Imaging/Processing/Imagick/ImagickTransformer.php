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
use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Core\StringStream;
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

    /** @var FileOperationsInterface */
    private $fileOperations;

    public function __construct(\Imagick $imagick, FileOperationsInterface $fileOperations)
    {
        $this->imagick = $imagick;
        $this->fileOperations = $fileOperations;
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

    public function getSize(): SizeInterface
    {
        return new Size($this->imagick->getImageWidth(), $this->imagick->getImageHeight());
    }

    public function getData(): StreamInterface
    {
        $data = $this->imagick->getImageBlob();

        return new StringStream($data);
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