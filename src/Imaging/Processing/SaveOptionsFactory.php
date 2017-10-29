<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Processing;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class SaveOptionsFactory implements SaveOptionsFactoryInterface
{
    /** @var int */
    private $quality = SaveOptions::QUALITY_VALUE_DEFAULT;

    public function getQuality(): int
    {
        return $this->quality;
    }

    public function setQuality(int $quality): void
    {
        $this->quality = $quality;
    }

    public function create(): SaveOptions
    {
        $saveOptions = new SaveOptions();
        $saveOptions->setQuality($this->quality);

        return $saveOptions;
    }
}
