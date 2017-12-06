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

use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Strider2038\ImgCache\Core\QueryParameter;
use Strider2038\ImgCache\Core\QueryParametersCollection;
use Strider2038\ImgCache\Core\Streaming\StreamFactoryInterface;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpMethodEnum;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Exception\BadApiResponseException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class YandexMapStorageDriver implements YandexMapStorageDriverInterface
{
    /** @var ClientInterface */
    private $client;

    /** @var StreamFactoryInterface */
    private $streamFactory;

    /** @var string */
    private $key;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(ClientInterface $client, StreamFactoryInterface $streamFactory, string $key = '')
    {
        $this->client = $client;
        $this->streamFactory = $streamFactory;
        $this->key = $key;
        $this->logger = new NullLogger();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function getMapContents(QueryParametersCollection $queryParameters): StreamInterface
    {
        $this->logger->info(sprintf(
            'Sending request to "%s" with parameters: %s.',
            $this->client->getConfig('base_uri'),
            json_encode($queryParameters->toArray())
        ));

        if ($this->key !== '') {
            $queryParameters = clone $queryParameters;
            $queryParameters->add(new QueryParameter('key', $this->key));
        }

        try {
            $response = $this->client->request(
                HttpMethodEnum::GET,
                '',
                [
                    RequestOptions::QUERY => $queryParameters->toArray()
                ]
            );
        } catch (\Exception $exception) {
            throw new BadApiResponseException(
                'Unexpected response from API.',
                HttpStatusCodeEnum::BAD_GATEWAY,
                $exception
            );
        }

        if ($response->getStatusCode() !== HttpStatusCodeEnum::OK) {
            throw new BadApiResponseException(
                sprintf(
                    'Unexpected response from API: %d %s.',
                    $response->getStatusCode(),
                    $response->getReasonPhrase()
                )
            );
        }

        $responseResource = $response->getBody()->detach();

        if ($responseResource === null) {
            throw new BadApiResponseException('Response has empty body.');
        }

        $this->logger->info('Successful response is received and response body returned.');

        return $this->streamFactory->createStreamFromResource($responseResource);
    }
}
