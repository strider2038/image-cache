<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Service\Image;

use Strider2038\ImgCache\Core\ActionInterface;
use Strider2038\ImgCache\Core\Http\ResponseFactoryInterface;
use Strider2038\ImgCache\Imaging\ImageCacheInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
abstract class AbstractImageAction implements ActionInterface
{
    /** @var ResponseFactoryInterface */
    protected $responseFactory;
    /** @var string */
    protected $location;
    /** @var ImageCacheInterface */
    protected $imageCache;

    public function __construct(string $location, ImageCacheInterface $imageCache, ResponseFactoryInterface $responseFactory)
    {
        $this->location = $location;
        $this->imageCache = $imageCache;
        $this->responseFactory = $responseFactory;
    }
}
