<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Core\Http;

use Strider2038\ImgCache\Collection\StringList;
use Strider2038\ImgCache\Core\Streaming\StreamFactoryInterface;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpMethodEnum;
use Strider2038\ImgCache\Enum\HttpProtocolVersionEnum;
use Strider2038\ImgCache\Enum\ResourceStreamModeEnum;
use Strider2038\ImgCache\Exception\InvalidRequestException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class RequestFactory implements RequestFactoryInterface
{
    /** @var StreamFactoryInterface */
    private $streamFactory;
    /** @var string */
    private $streamSource;

    public function __construct(StreamFactoryInterface $streamFactory, string $streamSource = 'php://input')
    {
        $this->streamFactory = $streamFactory;
        $this->streamSource = $streamSource;
    }

    public function createRequest(array $serverConfiguration): RequestInterface
    {
        $method = $this->getRequestMethod($serverConfiguration);
        $uri = $this->getRequestUri($serverConfiguration);
        $protocolVersion = $this->getProtocolVersion($serverConfiguration);
        $headers = $this->getHeaders($serverConfiguration);
        $bodyStream = $this->getRequestBody();

        $request = new Request($method, $uri);
        $request->setProtocolVersion($protocolVersion);
        $request->setBody($bodyStream);
        $request->setHeaders($headers);

        return $request;
    }

    private function getRequestMethod(array $serverConfiguration): HttpMethodEnum
    {
        $requestMethodName = strtoupper($serverConfiguration['REQUEST_METHOD'] ?? '');

        if (!HttpMethodEnum::isValid($requestMethodName)) {
            throw new InvalidRequestException(sprintf('Unsupported http method "%s".', $requestMethodName));
        }

        return new HttpMethodEnum($requestMethodName);
    }

    private function getRequestUri(array $serverConfiguration): Uri
    {
        return new Uri($serverConfiguration['REQUEST_URI'] ?? '');
    }

    private function getProtocolVersion(array $serverConfiguration): HttpProtocolVersionEnum
    {
        $requestProtocol = $serverConfiguration['SERVER_PROTOCOL'] ?? '';

        if ($requestProtocol === 'HTTP/1.0') {
            $protocolVersion = HttpProtocolVersionEnum::V1_0;
        } else {
            $protocolVersion = HttpProtocolVersionEnum::V1_1;
        }

        return new HttpProtocolVersionEnum($protocolVersion);
    }

    private function getRequestBody(): StreamInterface
    {
        $mode = new ResourceStreamModeEnum(ResourceStreamModeEnum::READ_ONLY);
        $bodyStream = $this->streamFactory->createStreamByParameters($this->streamSource, $mode);

        return $bodyStream;
    }

    private function getHeaders(array $serverConfiguration): HeaderCollection
    {
        $headers = new HeaderCollection();

        foreach ($serverConfiguration as $name => $value) {
            if (strncmp($name, 'HTTP_', 5) === 0) {
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers->set($name, new StringList([$value]));
            }
        }

        return $headers;
    }
}
