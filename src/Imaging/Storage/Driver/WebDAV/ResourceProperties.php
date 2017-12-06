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
class ResourceProperties
{
    /** @var string */
    private $href;

    /** @var string */
    private $status;

    /** @var \DateTimeInterface */
    private $creationDate;

    /** @var \DateTimeInterface */
    private $lastModified;

    /** @var string */
    private $displayName;

    /** @var int */
    private $contentLength;

    /** @var WebDAVResourceTypeEnum */
    private $resourceType;

    /** @var string */
    private $contentType;

    public function __construct(
        string $href,
        string $status,
        \DateTimeInterface $creationDate,
        \DateTimeInterface $lastModified,
        string $displayName,
        int $contentLength,
        WebDAVResourceTypeEnum $resourceType,
        string $contentType
    ) {
        $this->href = $href;
        $this->status = $status;
        $this->creationDate = $creationDate;
        $this->lastModified = $lastModified;
        $this->displayName = $displayName;
        $this->contentLength = $contentLength;
        $this->resourceType = $resourceType;
        $this->contentType = $contentType;
    }

    public function getHref(): string
    {
        return $this->href;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreationDate(): \DateTimeInterface
    {
        return $this->creationDate;
    }

    public function getLastModified(): \DateTimeInterface
    {
        return $this->lastModified;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function getContentLength(): int
    {
        return $this->contentLength;
    }

    public function getResourceType(): WebDAVResourceTypeEnum
    {
        return $this->resourceType;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }
}
