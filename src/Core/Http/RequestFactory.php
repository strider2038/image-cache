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

use Strider2038\ImgCache\Core\Streaming\StreamFactoryInterface;
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
        $requestMethodName = strtoupper($serverConfiguration['REQUEST_METHOD'] ?? '');
        if (!HttpMethodEnum::isValid($requestMethodName)) {
            throw new InvalidRequestException(sprintf('Unsupported http method "%s"', $requestMethodName));
        }

        $method = new HttpMethodEnum($requestMethodName);
        $uri = new Uri($serverConfiguration['REQUEST_URI'] ?? '');

        $request = new Request($method, $uri);

        $mode = new ResourceStreamModeEnum(ResourceStreamModeEnum::READ_ONLY);
        $bodyStream = $this->streamFactory->createStreamByParameters($this->streamSource, $mode);

        $request->setBody($bodyStream);

        $requestProtocol = $serverConfiguration['SERVER_PROTOCOL'] ?? '';
        if ($requestProtocol === 'HTTP/1.0') {
            $request->setProtocolVersion(new HttpProtocolVersionEnum(HttpProtocolVersionEnum::V1_0));
        } else {
            $request->setProtocolVersion(new HttpProtocolVersionEnum(HttpProtocolVersionEnum::V1_1));
        }

        return $request;
    }
}
