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

use Strider2038\ImgCache\Imaging\Transformation\TransformationsFactoryInterface;
use Strider2038\ImgCache\Imaging\Source\SourceInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageCache implements ImageCacheInterface
{
    /** @var Strider2038\ImgCache\Imaging\Source\SourceInterface */
    private $source;
    
    /** @var Strider2038\ImgCache\Imaging\Transformation\TransformationsFactoryInterface */
    private $transformationsFactory;
    
    public function __construct(
        SourceInterface $source, 
        TransformationsFactoryInterface $transformationsFactory
    ) {
        $this->source = $source;
        $this->transformationsFactory = $transformationsFactory;
    }

    public function get(string $key): ?Image
    {
        $imageRequest = new ImageRequest($this->transformationsFactory, $key);
        
        
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
    
}
