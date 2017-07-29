<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Source\Mapping;

use Strider2038\ImgCache\Imaging\Source\Key\FilenameKey;
use Strider2038\ImgCache\Imaging\Source\Key\FilenameKeyInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class DirectKeyMapper implements FilenameKeyMapperInterface
{
    public function getKey(string $filename): FilenameKeyInterface
    {
        $filenameKey = new FilenameKey($filename);

        return $filenameKey;
    }
}