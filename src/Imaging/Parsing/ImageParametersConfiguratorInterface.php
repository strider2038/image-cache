<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Parsing;

use Strider2038\ImgCache\Imaging\Image\ImageParameters;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface ImageParametersConfiguratorInterface
{
    public function updateSaveOptionsByConfiguration(ImageParameters $parameters, string $configuration): void;
}
