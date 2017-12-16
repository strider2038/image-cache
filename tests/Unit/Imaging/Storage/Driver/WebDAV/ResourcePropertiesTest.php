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

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Enum\WebDAVResourceTypeEnum;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\ResourceProperties;

class ResourcePropertiesTest extends TestCase
{
    const HREF = 'href';
    const STATUS = 'HTTP/1.1 200 OK';
    const CREATION_DATE = '2017-12-06T20:27:38Z';
    const CREATION_DATE_ATOM = '2017-12-06T20:27:38+00:00';
    const LAST_MODIFIED_DATE = 'Wed, 06 Dec 2017 20:27:38 GMT';
    const LAST_MODIFIED_DATE_ATOM = '2017-12-06T20:27:38+00:00';
    const DISPLAY_NAME = 'display_name';
    const CONTENT_LENGTH = 1;
    const CONTENT_TYPE = 'content_type';

    /** @test */
    public function construct_givenProperties_propertiesAreSetAndAccessible(): void
    {
        $properties = new ResourceProperties(
            self::HREF,
            self::STATUS,
            new \DateTimeImmutable(self::CREATION_DATE),
            new \DateTimeImmutable(self::LAST_MODIFIED_DATE),
            self::DISPLAY_NAME,
            self::CONTENT_LENGTH,
            new WebDAVResourceTypeEnum(WebDAVResourceTypeEnum::DIRECTORY),
            self::CONTENT_TYPE
        );

        $this->assertEquals(self::HREF, $properties->getHref());
        $this->assertEquals(self::STATUS, $properties->getStatus());
        $this->assertEquals(self::CREATION_DATE_ATOM, $properties->getCreationDate()->format(\DateTime::ATOM));
        $this->assertEquals(self::LAST_MODIFIED_DATE_ATOM, $properties->getLastModified()->format(\DateTime::ATOM));
        $this->assertEquals(self::DISPLAY_NAME, $properties->getDisplayName());
        $this->assertEquals(self::CONTENT_LENGTH, $properties->getContentLength());
        $this->assertEquals(WebDAVResourceTypeEnum::DIRECTORY, $properties->getResourceType()->getValue());
        $this->assertEquals(self::CONTENT_TYPE, $properties->getContentType());
    }
}
