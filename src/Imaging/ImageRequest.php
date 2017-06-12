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

use Strider2038\ImgCache\Exception\InvalidImageException;
use Strider2038\ImgCache\Imaging\Transformation\{
    Quality,
    TransformationsCollection,
    TransformationsFactoryInterface
};

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageRequest implements ImageRequestInterface
{
    /** @var string */
    protected $filename;
    
    /** @var TransformationsCollection */
    protected $transformations;

    /**
     * Quality of the final image
     * @var int
     */
    protected $quality;
    
    public function __construct(
        TransformationsFactoryInterface $factory, 
        string $filename
    ) {
        if (!preg_match('/^[A-Za-z0-9_\.\/]+$/', $filename) || $filename === '/') {
            throw new InvalidImageException(
                "Requested filename '{$filename}' contains illegal "
                . "characters or is empty"
            );
        }
        if (substr($filename, 0, 1) === '/') {
            $filename = substr($filename, 1);
        }
        
        $path = pathinfo($filename);
        if (!in_array(strtolower($path['extension']), static::getSupportedExtensions())) {
            throw new InvalidImageException(
                "Filename extension '{$path['extension']}' is not supported"
            );
        }
        
        $dirname = $path['dirname'] === '.' ? '' : "/{$path['dirname']}";
        foreach (explode('/', $dirname) as $dirpart) {
            if (strpos($dirpart, '.') !== false) {
                throw new InvalidImageException("Dots are not allowed in directory names");
            }
        }
        
        $filenameParts = explode('_', $path['filename']);
        $filenamePartsCount = count($filenameParts);
        
        $this->transformations = new TransformationsCollection();
        if ($filenamePartsCount > 1) {
            for ($i = 1; $i < $filenamePartsCount; $i++) {
                $transformation = $factory->create($filenameParts[$i]);
                if ($transformation instanceof Quality) {
                    $this->quality = $transformation->getValue();
                } else {
                    $this->transformations->add($transformation);
                }
            }
        }
        
        $this->filename = "{$dirname}/{$filenameParts[0]}.{$path['extension']}";
    }
    
    public function getFileName(): string
    {
        return $this->filename;
    }
    
    public function getTransformations(): TransformationsCollection
    {
        return $this->transformations;
    }
    
    public function getQuality(): ?int
    {
        return $this->quality;
    }
    
    public static function getSupportedExtensions(): array
    {
        return ['jpg', 'jpeg'];
    }
}
