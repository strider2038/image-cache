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

use Strider2038\ImgCache\Imaging\Transformation\TransformationsFactoryInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ThumbnailKeyParserFactory implements ThumbnailKeyParserFactoryInterface
{
    /** @var TransformationsFactoryInterface */
    private $transformationsFactory;

    public function __construct(TransformationsFactoryInterface $transformationsFactory)
    {
        $this->transformationsFactory = $transformationsFactory;
    }

    public function create(string $filename): ThumbnailKeyParserInterface
    {
        return new ThumbnailKeyParser($this->transformationsFactory, $filename);
    }
}