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

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Strider2038\ImgCache\Core\Streaming\StreamFactoryInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class GuzzleClientFactory implements HttpClientFactoryInterface
{
    /** @var StreamFactoryInterface */
    private $streamFactory;
    /** @var HandlerStack */
    private $handlerStack;
    /** @var string */
    private $userAgent = 'Image Caching Microservice';

    public function __construct(
        StreamFactoryInterface $streamFactory,
        HandlerStack $handlerStack = null
    ) {
        $this->streamFactory = $streamFactory;
        $this->handlerStack = $handlerStack;
    }

    public function setUserAgent(string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }

    public function createClient(array $parameters = []): HttpClientInterface
    {
        $clientParameters = $this->getCombinedClientParametersWithHandlerStack($parameters);

        return new GuzzleClientAdapter(
            new Client($clientParameters),
            $this->streamFactory
        );
    }

    private function getCombinedClientParametersWithHandlerStack(array $parameters): array
    {
        $clientParameters = array_replace_recursive(
            $this->getDefaultParameters(),
            $parameters
        );

        if ($this->handlerStack) {
            $clientParameters['handler'] = $this->handlerStack;
        }

        return $clientParameters;
    }

    private function getDefaultParameters(): array
    {
        return [
            'headers' => [
                'User-Agent' => $this->userAgent,
            ],
        ];
    }
}
