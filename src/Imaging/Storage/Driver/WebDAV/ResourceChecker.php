<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV;

use Strider2038\ImgCache\Enum\WebDAVResourceTypeEnum;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ResourceChecker implements ResourceCheckerInterface
{
    /** @var ResourcePropertiesGetterInterface */
    private $propertiesGetter;

    public function __construct(ResourcePropertiesGetterInterface $propertiesGetter)
    {
        $this->propertiesGetter = $propertiesGetter;
    }

    public function isFile(string $resourceUri): bool
    {
        return $this->resourceIsOfType($resourceUri, WebDAVResourceTypeEnum::FILE);
    }

    public function isDirectory(string $resourceUri): bool
    {
        return $this->resourceIsOfType($resourceUri, WebDAVResourceTypeEnum::DIRECTORY);
    }

    private function resourceIsOfType(string $resourceUri, string $resourceType): bool
    {
        $propertiesCollection = $this->propertiesGetter->getResourcePropertiesCollection($resourceUri);
        $isOfType = false;

        if ($propertiesCollection->count() === 1) {
            /** @var ResourceProperties $properties */
            $properties = $propertiesCollection->first();
            $isOfType = $properties->getResourceType()->getValue() === $resourceType;
        }

        return $isOfType;
    }
}
