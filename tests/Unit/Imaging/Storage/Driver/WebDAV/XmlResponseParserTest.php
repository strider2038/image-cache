<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Storage\Driver\WebDAV;

use Strider2038\ImgCache\Enum\WebDAVResourceTypeEnum;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\ResourceProperties;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\ResourcePropertiesCollection;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\XmlResponseParser;
use Strider2038\ImgCache\Tests\Support\FileTestCase;

class XmlResponseParserTest extends FileTestCase
{
    private const RESOURCES_COUNT = 2;
    private const DIRECTORY_HREF = '/imgcache/';
    private const DIRECTORY_STATUS = 'HTTP/1.1 200 OK';
    private const DIRECTORY_CREATION_DATE = '2017-12-06T20:27:38+00:00';
    private const DIRECTORY_LAST_MODIFIED = '2017-12-10T09:38:59+00:00';
    private const DIRECTORY_DISPLAY_NAME = 'imgcache';
    private const DIRECTORY_CONTENT_LENGTH = 0;
    private const DIRECTORY_CONTENT_TYPE = '';
    private const FILE_HREF = '/imgcache/file.json';
    private const FILE_STATUS = 'HTTP/1.1 200 OK';
    private const FILE_CREATION_DATE = '2017-12-06T20:27:38+00:00';
    private const FILE_LAST_MODIFIED = '2017-12-10T09:38:59+00:00';
    private const FILE_DISPLAY_NAME = 'file.json';
    private const FILE_CONTENT_LENGTH = 16;
    private const FILE_CONTENT_TYPE = 'text/plain';

    /** @test */
    public function parseResponse_givenResponse_resourcePropertiesCollectionWithExpectedValuesReturned(): void
    {
        $parser = new XmlResponseParser();
        $response = $this->getResponseExample();

        $resourcePropertiesCollection = $parser->parseResponse($response);

        $this->assertInstanceOf(ResourcePropertiesCollection::class, $resourcePropertiesCollection);
        $this->assertCount(self::RESOURCES_COUNT, $resourcePropertiesCollection);
        $this->assertResourcePropertiesHasExpectedValuesForDirectory($resourcePropertiesCollection->first());
        $this->assertResourcePropertiesHasExpectedValuesForFile($resourcePropertiesCollection->last());
    }

    private function getResponseExample(): string
    {
        return file_get_contents($this->givenAssetFilename(self::FILE_WEBDAV_RESPONSE_XML));
    }

    private function assertResourcePropertiesHasExpectedValuesForDirectory(ResourceProperties $resourceProperties): void
    {
        $this->assertEquals(self::DIRECTORY_HREF, $resourceProperties->getHref());
        $this->assertEquals(self::DIRECTORY_STATUS, $resourceProperties->getStatus());
        $this->assertEquals(self::DIRECTORY_CREATION_DATE, $resourceProperties->getCreationDate()->format(\DateTime::ATOM));
        $this->assertEquals(self::DIRECTORY_LAST_MODIFIED, $resourceProperties->getLastModified()->format(\DateTime::ATOM));
        $this->assertEquals(self::DIRECTORY_DISPLAY_NAME, $resourceProperties->getDisplayName());
        $this->assertEquals(self::DIRECTORY_CONTENT_LENGTH, $resourceProperties->getContentLength());
        $this->assertEquals(WebDAVResourceTypeEnum::DIRECTORY, $resourceProperties->getResourceType()->getValue());
        $this->assertEquals(self::DIRECTORY_CONTENT_TYPE, $resourceProperties->getContentType());
    }

    private function assertResourcePropertiesHasExpectedValuesForFile(ResourceProperties $resourceProperties): void
    {
        $this->assertEquals(self::FILE_HREF, $resourceProperties->getHref());
        $this->assertEquals(self::FILE_STATUS, $resourceProperties->getStatus());
        $this->assertEquals(self::FILE_CREATION_DATE, $resourceProperties->getCreationDate()->format(\DateTime::ATOM));
        $this->assertEquals(self::FILE_LAST_MODIFIED, $resourceProperties->getLastModified()->format(\DateTime::ATOM));
        $this->assertEquals(self::FILE_DISPLAY_NAME, $resourceProperties->getDisplayName());
        $this->assertEquals(self::FILE_CONTENT_LENGTH, $resourceProperties->getContentLength());
        $this->assertEquals(WebDAVResourceTypeEnum::FILE, $resourceProperties->getResourceType()->getValue());
        $this->assertEquals(self::FILE_CONTENT_TYPE, $resourceProperties->getContentType());
    }
}
