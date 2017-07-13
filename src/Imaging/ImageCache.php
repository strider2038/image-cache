<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging;

use Strider2038\ImgCache\Imaging\Processing\ProcessingImageInterface;
use Strider2038\ImgCache\Imaging\Source\SourceInterface;
use Strider2038\ImgCache\Imaging\Processing\ProcessingEngineInterface;
use Strider2038\ImgCache\Imaging\Transformation\{
    TransformationInterface,
    TransformationsFactoryInterface,
    TransformationsCollection
};
use Strider2038\ImgCache\Exception\{
    InvalidConfigException,
    ApplicationException
};

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageCache implements ImageCacheInterface
{
    /**
     * Web directory that contains image files
     * @var string
     */
    private $cacheDirectory;
    
    /** @var SourceInterface */
    private $source;
    
    /** @var TransformationsFactoryInterface */
    private $transformationsFactory;
    
    /** @var ProcessingEngineInterface */
    private $processingEngine;
    
    public function __construct(
        string $cacheDirectory,    
        SourceInterface $source, 
        TransformationsFactoryInterface $transformationsFactory,
        ProcessingEngineInterface $processingEngine
    ) {
        if (!is_dir($cacheDirectory)) {
            throw new InvalidConfigException("Directory '{$cacheDirectory}' does not exist");
        }
        $this->cacheDirectory = rtrim($cacheDirectory, '/');
        $this->source = $source;
        $this->transformationsFactory = $transformationsFactory;
        $this->processingEngine = $processingEngine;
    }

    /**
     * First request for image file extracts image from the source and puts it into cache
     * directory. Next requests will be processed by nginx.
     * @param string $key
     * @return null|Image
     * @throws ApplicationException
     */
    public function get(string $key): ?Image
    {
        $imageRequest = new ImageRequest($this->transformationsFactory, $key);
        
        $sourceImage = $this->source->get($imageRequest->getFileName());
        if ($sourceImage === null) {
            return null;
        }
        
        $transformations = $imageRequest->getTransformations();

        $destinationFilename = $this->cacheDirectory . $key;

        if (count($transformations) <= 0) {
            $sourceFilename = $sourceImage->getFilename();

            if (!copy($sourceFilename, $destinationFilename)) {
                throw new ApplicationException(
                    "Cannot copy file '{$sourceFilename}' to '{$destinationFilename}'"
                );
            }
        } else {
            $transformedImage = $this->getTransformedImage($sourceImage, $transformations);
            $transformedImage->save($destinationFilename);
            // @todo save options
        }

        return new Image($destinationFilename);
    }

    public function put(string $key, $data): void
    {
        
    }
    
    public function delete(string $key): void
    {
        
    }
    
    public function exists(string $key): bool
    {
        
    }
    
    protected function getTransformedImage(Image $image, TransformationsCollection $transformations): ProcessingImageInterface
    {
        $processingImage = $this->processingEngine->open($image->getFilename());
        /** @var TransformationInterface[] $transformations */
        foreach ($transformations as $transformation) {
            $transformation->apply($processingImage);
        }
        return $processingImage;
    }
}
