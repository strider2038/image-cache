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
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Core\QueryParameterCollection;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpMethodEnum;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Exception\BadApiResponseException;
use Strider2038\ImgCache\Utility\HttpClientInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ApiStorageDriver implements ApiStorageDriverInterface
{
    /** @var HttpClientInterface */
    private $client;

    /** @var QueryParameterCollection */
    private $additionalQueryParameters;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        HttpClientInterface $client,
        QueryParameterCollection $additionalParameters = null
    ) {
        $this->client = $client;
        $this->additionalQueryParameters = $additionalParameters ?? new QueryParameterCollection();
        $this->logger = new NullLogger();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function getImageContents(QueryParameterCollection $queryParameterCollection): StreamInterface
    {
        $this->logger->info(sprintf(
            'Sending request for map image with parameters: %s.',
            json_encode($queryParameterCollection->toArray())
        ));

        $query = $this->collectQueryParameters($queryParameterCollection);
        $response = $this->makeApiRequest($query);
        $this->validateResponseIsOk($response);
        $body = $response->getBody();

        $this->logger->info('Successful response is received and response body returned.');

        return $body;
    }

    private function collectQueryParameters(QueryParameterCollection $queryParameterCollection): QueryParameterCollection
    {
        $query = clone $queryParameterCollection;
        $query->append($this->additionalQueryParameters);

        return $query;
    }

    private function makeApiRequest(QueryParameterCollection $queryParameterCollection): ResponseInterface
    {
        return $this->client->request(
            HttpMethodEnum::GET,
            '',
            [
                RequestOptions::QUERY => $queryParameterCollection->toArray()
            ]
        );
    }

    private function validateResponseIsOk(ResponseInterface $response): void
    {
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
    }
}
