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

use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Enum\WebDAVMethodEnum;
use Strider2038\ImgCache\Exception\BadApiResponseException;
use Strider2038\ImgCache\Exception\FileNotFoundException;
use Strider2038\ImgCache\Utility\HttpClientInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ResourceManipulator implements ResourceManipulatorInterface
{
    /** @var HttpClientInterface */
    private $client;

    /** @var RequestOptionsFactoryInterface */
    private $requestOptionsFactory;

    public function __construct(HttpClientInterface $client, RequestOptionsFactoryInterface $requestOptionsFactory)
    {
        $this->client = $client;
        $this->requestOptionsFactory = $requestOptionsFactory;
    }

    public function getResource(string $resourceUri): StreamInterface
    {
        $response = $this->client->request(WebDAVMethodEnum::GET, $resourceUri);

        $statusCode = $response->getStatusCode()->getValue();

        if ($statusCode === HttpStatusCodeEnum::NOT_FOUND) {
            throw new FileNotFoundException(sprintf('File "%s" not found in storage.', $resourceUri));
        }

        if ($statusCode !== HttpStatusCodeEnum::OK) {
            throw new BadApiResponseException(
                sprintf(
                    'Unexpected response from API: %d %s.',
                    $statusCode,
                    $response->getReasonPhrase()
                )
            );
        }

        return $response->getBody();
    }

    public function putResource(string $resourceUri, StreamInterface $contents): void
    {
        $requestOptions = $this->requestOptionsFactory->createPutOptions($contents);
        $response = $this->client->request(WebDAVMethodEnum::PUT, $resourceUri, $requestOptions);
        $statusCode = $response->getStatusCode()->getValue();

        if ($statusCode !== HttpStatusCodeEnum::CREATED) {
            throw new BadApiResponseException(
                sprintf(
                    'Unexpected response from API: %d %s.',
                    $statusCode,
                    $response->getReasonPhrase()
                )
            );
        }
    }
}
