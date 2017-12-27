<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Parsing\Filename;

use Strider2038\ImgCache\Imaging\Parsing\Filename\PlainFilename;
use Strider2038\ImgCache\Imaging\Processing\ProcessingConfiguration;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailFilename extends PlainFilename
{
    /** @var ProcessingConfiguration */
    private $processingConfiguration;

    /** @var string */
    private $mask;

    public function __construct(
        string $value,
        string $mask,
        ProcessingConfiguration $processingConfiguration
    ) {
        parent::__construct($value);
        $this->mask = $mask;
        $this->processingConfiguration = $processingConfiguration;
    }

    public function getMask(): string
    {
        return $this->mask;
    }

    public function getProcessingConfiguration(): ProcessingConfiguration
    {
        return $this->processingConfiguration;
    }

    public function hasProcessingConfiguration(): bool
    {
        return !$this->processingConfiguration->isDefault();
    }
}
