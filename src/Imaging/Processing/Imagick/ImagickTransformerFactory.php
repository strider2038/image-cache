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
use Strider2038\ImgCache\Imaging\Processing\ImageTransformerFactoryInterface;
use Strider2038\ImgCache\Imaging\Processing\ImageTransformerInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImagickTransformerFactory implements ImageTransformerFactoryInterface
{
    /** @var FileOperationsInterface */
    private $fileOperations;

    /** @var StreamFactoryInterface */
    private $streamFactory;

    public function __construct(FileOperationsInterface $fileOperations, StreamFactoryInterface $streamFactory)
    {
        $this->fileOperations = $fileOperations;
        $this->streamFactory = $streamFactory;
    }

    public function createTransformer(StreamInterface $stream): ImageTransformerInterface
    {
        $imagick = new \Imagick();
        $contents = $stream->getContents();
        $imagick->readImageBlob($contents);

        return new ImagickTransformer($imagick, $this->fileOperations, $this->streamFactory);
    }
}
