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

use Strider2038\ImgCache\Imaging\Processing\ProcessingConfigurationInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailKey implements ThumbnailKeyInterface
{
    /** @var string */
    private $publicFilename;

    /** @var ProcessingConfigurationInterface */
    private $processingConfiguration;

    public function __construct(string $publicFilename, ProcessingConfigurationInterface $processingConfiguration)
    {
        $this->publicFilename = $publicFilename;
        $this->processingConfiguration = $processingConfiguration;
    }

    public function getPublicFilename(): string
    {
        return $this->publicFilename;
    }

    public function getProcessingConfiguration(): ProcessingConfigurationInterface
    {
        return $this->processingConfiguration;
    }

    public function hasProcessingConfiguration(): bool
    {
        return !$this->processingConfiguration->isDefault();
    }
}
