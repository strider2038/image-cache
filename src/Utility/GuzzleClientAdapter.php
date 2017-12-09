<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Utility;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\StreamInterface as PsrStreamInterface;
use Strider2038\ImgCache\Core\Http\HeaderCollection;
use Strider2038\ImgCache\Core\Http\HeaderValueCollection;
use Strider2038\ImgCache\Core\Http\Response;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Core\Streaming\StreamFactoryInterface;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Exception\BadApiResponseException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class GuzzleClientAdapter implements HttpClientInterface
{
    /** @var ClientInterface */
    private $client;

    /** @var StreamFactoryInterface */
    private $streamFactory;

    public function __construct(ClientInterface $client, StreamFactoryInterface $streamFactory)
    {
        $this->client = $client;
        $this->streamFactory = $streamFactory;
    }

    public function request(string $method, string $uri = '', array $options = []): ResponseInterface
    {
        try {
            $psrResponse = $this->client->request($method, $uri, $options);
        } catch (ClientException $exception) {
            $psrResponse = $exception->getResponse();
        }

        $response = $this->createResponseWithStatusCode($psrResponse->getStatusCode());

        $headerCollection = $this->createHeaderCollectionFromRawHeaders($psrResponse->getHeaders());
        $body = $this->getConvertedBodyStream($psrResponse->getBody());

        $response->setBody($body);
        $response->setHeaders($headerCollection);

        return $response;
    }

    private function createResponseWithStatusCode(int $statusCode): Response
    {
        $statusCodeEnum = new HttpStatusCodeEnum($statusCode);

        return new Response($statusCodeEnum);
    }

    private function getConvertedBodyStream(PsrStreamInterface $psrBody): StreamInterface
    {
        $resource = $psrBody->detach();

        if ($resource === null) {
            throw new BadApiResponseException('Api response has empty body.');
        }

        return $this->streamFactory->createStreamFromResource($resource);
    }

    private function createHeaderCollectionFromRawHeaders(array $headers): HeaderCollection
    {
        $headerCollection = new HeaderCollection();

        foreach ($headers as $headerName => $values) {
            /** @var string[] $values */

            $headerValueCollection = new HeaderValueCollection();

            foreach ($values as $value) {
                $headerValueCollection->add($value);
            }

            $headerCollection->set($headerName, $headerValueCollection);
        }

        return $headerCollection;
    }
}
