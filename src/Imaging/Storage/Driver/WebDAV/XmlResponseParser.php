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
class XmlResponseParser implements ResponseParserInterface
{
    public function parseResponse(string $response): ResourcePropertiesCollection
    {
        $collection = new ResourcePropertiesCollection();
        $xml = simplexml_load_string($response);

        foreach ($xml->children('DAV:') as $element) {
            $collection->add(new ResourceProperties(
                $element->href->__toString(),
                $element->propstat->status->__toString(),
                new \DateTimeImmutable($element->propstat->prop->creationdate->__toString()),
                new \DateTimeImmutable($element->propstat->prop->getlastmodified->__toString()),
                $element->propstat->prop->displayname->__toString(),
                (int) $element->propstat->prop->getcontentlength,
                $this->getResourceTypeByPropertyValue((bool) $element->propstat->prop->resourcetype->collection),
                $element->propstat->prop->getcontenttype->__toString()
            ));
        }

        return $collection;
    }

    private function getResourceTypeByPropertyValue(bool $isCollection): WebDAVResourceTypeEnum
    {
        $type = $isCollection ? WebDAVResourceTypeEnum::DIRECTORY : WebDAVResourceTypeEnum::FILE;

        return new WebDAVResourceTypeEnum($type);
    }
}
