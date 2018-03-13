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
use Strider2038\ImgCache\Configuration\ImageSource\ImageSourceCollection;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Exception\ImageSourceNotFoundException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageSourceDetector implements ImageSourceDetectorInterface
{
    /** @var ImageSourceCollection */
    private $imageSourceCollection;

    /** @var string */
    private $urlPath;

    public function __construct(ImageSourceCollection $imageSourceCollection)
    {
        $this->imageSourceCollection = $imageSourceCollection;
    }

    public function detectImageSourceByRequest(RequestInterface $request): AbstractImageSource
    {
        $this->getUrlPathFromRequest($request);
        $imageSource = $this->findImageSourceByPath();

        if ($imageSource === null) {
            $this->throwNotFoundException();
        }

        return $imageSource;
    }

    private function getUrlPathFromRequest(RequestInterface $request): void
    {
        $uri = $request->getUri();
        $this->urlPath = $uri->getPath();
    }

    private function findImageSourceByPath(): ?AbstractImageSource
    {
        $foundImageSource = null;

        /** @var AbstractImageSource $imageSource */
        foreach ($this->imageSourceCollection as $imageSource) {
            $detectionPattern = $this->createDetectionPatternByImageSource($imageSource);
            if (preg_match($detectionPattern, $this->urlPath)) {
                $foundImageSource = $imageSource;
                break;
            }
        }

        return $foundImageSource;
    }

    private function createDetectionPatternByImageSource(AbstractImageSource $imageSource): string
    {
        $cacheDirectory = $imageSource->getCacheDirectory();

        return sprintf('/^%s.*$/', str_replace('/', '\\/', $cacheDirectory));
    }

    private function throwNotFoundException(): void
    {
        throw new ImageSourceNotFoundException(
            sprintf(
                'Image source was not found for given url path: %s.',
                $this->urlPath
            )
        );
    }
}
