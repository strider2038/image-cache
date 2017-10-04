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

use Strider2038\ImgCache\Imaging\Parsing\Source\SourceKeyInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingConfigurationInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface ThumbnailKeyInterface extends SourceKeyInterface
{
    public function getProcessingConfiguration(): ProcessingConfigurationInterface;
    public function hasProcessingConfiguration(): bool;
    public function getThumbnailMask(): string;
}