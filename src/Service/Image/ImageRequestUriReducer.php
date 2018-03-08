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

use Strider2038\ImgCache\Configuration\ImageSource\AbstractImageSource;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\Uri;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageRequestUriReducer implements ImageRequestTransformerInterface
{
    /** @var string */
    private $uriPath;

    public function transformRequestForImageSource(
        RequestInterface $request,
        AbstractImageSource $imageSource
    ): RequestInterface {
        $this->extractUriPathFromRequest($request);
        $reducedUri = $this->getUriWithPathReducedByCacheDirectory($imageSource);

        return $request->withUri($reducedUri);
    }

    private function extractUriPathFromRequest(RequestInterface $request): void
    {
        $uri = $request->getUri();
        $this->uriPath = $uri->getPath();
    }

    private function getUriWithPathReducedByCacheDirectory(AbstractImageSource $imageSource): Uri
    {
        $cacheDirectory = $imageSource->getCacheDirectory();

        $reducingPattern = sprintf('/^%s/', str_replace('/', '\/', $cacheDirectory));
        $reducedPath = preg_replace($reducingPattern, '', $this->uriPath);

        return new Uri($reducedPath);
    }
}
