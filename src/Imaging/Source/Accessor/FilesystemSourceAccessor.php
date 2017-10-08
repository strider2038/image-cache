<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Source\Accessor;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Imaging\Image\ImageInterface;
use Strider2038\ImgCache\Imaging\Source\FilesystemSourceInterface;
use Strider2038\ImgCache\Imaging\Source\Key\FilenameKeyInterface;
use Strider2038\ImgCache\Imaging\Source\Mapping\FilenameKeyMapperInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FilesystemSourceAccessor implements SourceAccessorInterface
{
    /** @var FilesystemSourceInterface */
    private $source;

    /** @var FilenameKeyMapperInterface */
    private $keyMapper;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        FilesystemSourceInterface $source,
        FilenameKeyMapperInterface $keyMapper
    ) {
        $this->source = $source;
        $this->keyMapper = $keyMapper;
        $this->logger = new NullLogger();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function get(string $key): ? ImageInterface
    {
        $filenameKey = $this->composeFilenameKey($key);

        $image = $this->source->get($filenameKey);

        $this->logger->info(sprintf(
            'Image %s in filesystem source for key "%s"',
            $image === null ? 'not found' : 'is found',
            $key
        ));

        return $image;
    }

    public function exists(string $key): bool
    {
        $filenameKey = $this->composeFilenameKey($key);

        $exists = $this->source->exists($filenameKey);

        $this->logger->info(sprintf(
            'Image with key "%s" %s in filesystem source',
            $key,
            $exists ? 'exists' : 'does not exist'
        ));

        return $exists;
    }

    public function put(string $key, StreamInterface $stream): void
    {
        $filenameKey = $this->composeFilenameKey($key);
        $this->source->put($filenameKey, $stream);

        $this->logger->info(sprintf(
            "Image is successfully putted to source under key '%s'",
            $key
        ));
    }

    public function delete(string $key): void
    {
        $filenameKey = $this->composeFilenameKey($key);
        $this->source->delete($filenameKey);

        $this->logger->info(sprintf(
            "Image with key '%s' is successfully deleted from source",
            $key
        ));
    }

    private function composeFilenameKey(string $key): FilenameKeyInterface
    {
        $filenameKey = $this->keyMapper->getKey($key);

        $this->logger->info(sprintf('Key "%s" is mapped to "%s"', $key, $filenameKey->getValue()));

        return $filenameKey;
    }
}
