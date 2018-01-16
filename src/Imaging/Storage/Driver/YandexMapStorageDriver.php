<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Storage\Driver;

use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Strider2038\ImgCache\Core\QueryParameter;
use Strider2038\ImgCache\Core\QueryParameterCollection;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpMethodEnum;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Exception\BadApiResponseException;
use Strider2038\ImgCache\Utility\HttpClientInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class YandexMapStorageDriver implements YandexMapStorageDriverInterface
{
    /** @var HttpClientInterface */
    private $client;

    /** @var string */
    private $key;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(HttpClientInterface $client, string $key = '')
    {
        $this->client = $client;
        $this->key = $key;
        $this->logger = new NullLogger();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function getMapContents(QueryParameterCollection $queryParameters): StreamInterface
    {
        $this->logger->info(sprintf(
            'Sending request for map image with parameters: %s.',
            json_encode($queryParameters->toArray())
        ));

        if ($this->key !== '') {
            $queryParameters = clone $queryParameters;
            $queryParameters->add(new QueryParameter('key', $this->key));
        }

        $response = $this->client->request(
            HttpMethodEnum::GET,
            '',
            [
                RequestOptions::QUERY => $queryParameters->toArray()
            ]
        );

        $statusCode = $response->getStatusCode()->getValue();

        if ($statusCode !== HttpStatusCodeEnum::OK) {
            throw new BadApiResponseException(
                sprintf(
                    'Unexpected response from API: %d %s.',
                    $statusCode,
                    $response->getReasonPhrase()
                )
            );
        }

        $body = $response->getBody();

        $this->logger->info('Successful response is received and response body returned.');

        return $body;
    }
}
