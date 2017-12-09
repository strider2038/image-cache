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

use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Enum\WebDAVMethodEnum;
use Strider2038\ImgCache\Exception\BadApiResponseException;
use Strider2038\ImgCache\Utility\HttpClientInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ResourcePropertiesGetter implements ResourcePropertiesGetterInterface
{
    /** @var HttpClientInterface */
    private $client;

    /** @var ResponseParserInterface */
    private $responseParser;

    public function __construct(HttpClientInterface $client, ResponseParserInterface $responseParser)
    {
        $this->client = $client;
        $this->responseParser = $responseParser;
    }

    public function getResourcePropertiesCollection(string $resourceUri): ResourcePropertiesCollection
    {
        $response = $this->client->request(
            WebDAVMethodEnum::PROPFIND,
            $resourceUri,
            [
                'headers' => [
                    'Depth' => '0',
                ],
            ]
        );

        $statusCode = $response->getStatusCode()->getValue();

        if ($statusCode === HttpStatusCodeEnum::NOT_FOUND) {
            $resourcePropertiesCollection = new ResourcePropertiesCollection();
        } elseif ($statusCode === HttpStatusCodeEnum::MULTI_STATUS) {
            $body = $response->getBody();
            $contents = $body->getContents();
            $resourcePropertiesCollection = $this->responseParser->parseResponse($contents);
        } else {
            throw new BadApiResponseException(
                sprintf(
                    'Unexpected response from API: %d %s.',
                    $statusCode,
                    $response->getReasonPhrase()
                )
            );
        }

        return $resourcePropertiesCollection;
    }
}
