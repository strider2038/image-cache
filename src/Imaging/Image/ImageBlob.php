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

use Strider2038\ImgCache\Imaging\Processing\ProcessingEngineInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;


/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageBlob extends AbstractImage implements ImageInterface
{
    /** @var string */
    private $data;

    public function __construct(string $data, SaveOptions $saveOptions)
    {
        $this->data = $data;
        parent::__construct($saveOptions);
    }

    public function saveTo(string $filename): void
    {
        // TODO: Implement saveTo() method.
    }

    public function open(ProcessingEngineInterface $engine): ProcessingImageInterface
    {
        // TODO: Implement open() method.
    }

    public function render(): void
    {
        // TODO: Implement render() method.
    }
}