<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Parsing\Thumbnail;

use Strider2038\ImgCache\Imaging\Parsing\Filename\PlainFilename;
use Strider2038\ImgCache\Imaging\Processing\ProcessingConfiguration;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailKey extends PlainFilename
{
    /** @var ProcessingConfiguration */
    private $processingConfiguration;

    /** @var string */
    private $thumbnailMask;

    public function __construct(
        string $value,
        string $thumbnailMask,
        ProcessingConfiguration $processingConfiguration
    ) {
        parent::__construct($value);
        $this->thumbnailMask = $thumbnailMask;
        $this->processingConfiguration = $processingConfiguration;
    }

    public function getProcessingConfiguration(): ProcessingConfiguration
    {
        return $this->processingConfiguration;
    }

    public function hasProcessingConfiguration(): bool
    {
        return !$this->processingConfiguration->isDefault();
    }

    public function getThumbnailMask(): string
    {
        return $this->thumbnailMask;
    }
}
