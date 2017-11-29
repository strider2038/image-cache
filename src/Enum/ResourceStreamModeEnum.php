<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Enum;

use MyCLabs\Enum\Enum;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ResourceStreamModeEnum extends Enum
{
    public const READ_ONLY = 'rb';
    public const READ_AND_WRITE = 'r+b';
    public const WRITE_ONLY = 'wb';
    public const WRITE_AND_READ = 'w+b';
    public const APPEND_ONLY = 'ab';
    public const APPEND_AND_READ = 'a+b';
    public const WRITE_IF_NOT_EXIST = 'xb';
    public const WRITE_AND_READ_IF_NOT_EXIST = 'x+b';
    public const WRITE_WITHOUT_TRUNCATE = 'cb';
    public const WRITE_AND_READ_WITHOUT_TRUNCATE = 'c+b';

    public function isReadable(): bool
    {
        return \in_array(
            $this->value,
            [
                self::READ_ONLY,
                self::READ_AND_WRITE,
                self::WRITE_AND_READ,
                self::APPEND_AND_READ,
                self::WRITE_AND_READ_IF_NOT_EXIST,
                self::WRITE_AND_READ_WITHOUT_TRUNCATE,
            ],
            true
        );
    }

    public function isWritable(): bool
    {
        return \in_array(
            $this->value,
            [
                self::READ_AND_WRITE,
                self::READ_AND_WRITE,
                self::WRITE_ONLY,
                self::WRITE_AND_READ,
                self::APPEND_ONLY,
                self::APPEND_AND_READ,
                self::WRITE_IF_NOT_EXIST,
                self::WRITE_AND_READ_IF_NOT_EXIST,
                self::WRITE_WITHOUT_TRUNCATE,
                self::WRITE_AND_READ_WITHOUT_TRUNCATE,
            ],
            true
        );
    }
}
