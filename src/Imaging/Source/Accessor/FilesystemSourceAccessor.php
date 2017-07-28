<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Source\Accessor;

use Strider2038\ImgCache\Imaging\Image\ImageInterface;
use Strider2038\ImgCache\Imaging\Source\FilesystemSourceInterface;
use Strider2038\ImgCache\Imaging\Source\Mapping\FilenameKeyMapperInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FilesystemSourceAccessor implements SourceAccessorInterface
{
    /** @var FilesystemSourceInterface */
    private $source;

    /** @var FilenameKeyMapperInterface */
    private $keyMapper;

    public function __construct(
        FilesystemSourceInterface $source,
        FilenameKeyMapperInterface $keyMapper
    ) {
        $this->source = $source;
        $this->keyMapper = $keyMapper;
    }

    public function get(string $key): ?ImageInterface
    {
        // TODO: Implement get() method.
    }

    public function exists(string $key): bool
    {
        // TODO: Implement exists() method.
    }
}