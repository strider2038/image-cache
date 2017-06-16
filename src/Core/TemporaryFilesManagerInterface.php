<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Core;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface TemporaryFilesManagerInterface
{
    public function getFilename(string $fileKey): ?string;
    public function putFile(string $fileKey, $data): string;
}
