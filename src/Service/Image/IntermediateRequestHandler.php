<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Service\Image;

use Strider2038\ImgCache\Configuration\ImageSource\AbstractImageSource;
use Strider2038\ImgCache\Core\Http\RequestHandlerInterface;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class IntermediateRequestHandler implements RequestHandlerInterface
{
    /** @var ImageSourceDetectorInterface */
    private $imageSourceDetector;

    /** @var ImageRequestHandlerFactoryInterface */
    private $requestHandlerFactory;

    /** @var ImageRequestTransformerInterface */
    private $requestTransformer;

    /** @var AbstractImageSource */
    private $detectedImageSource;

    public function __construct(
        ImageSourceDetectorInterface $imageSourceDetector,
        ImageRequestHandlerFactoryInterface $requestHandlerFactory,
        ImageRequestTransformerInterface $requestTransformer
    ) {
        $this->imageSourceDetector = $imageSourceDetector;
        $this->requestHandlerFactory = $requestHandlerFactory;
        $this->requestTransformer = $requestTransformer;
    }

    public function handleRequest(RequestInterface $request): ResponseInterface
    {
        $this->detectImageSourceByRequest($request);
        $concreteRequestHandler = $this->createConcreteRequestHandlerByRequestAndDetectedImageSource($request);
        $transformedRequest = $this->transformRequestForDetectedImageSource($request);

        return $concreteRequestHandler->handleRequest($transformedRequest);
    }

    private function detectImageSourceByRequest(RequestInterface $request): void
    {
        $this->detectedImageSource = $this->imageSourceDetector->detectImageSourceByRequest($request);
    }

    private function createConcreteRequestHandlerByRequestAndDetectedImageSource(RequestInterface $request): RequestHandlerInterface
    {
        $parameters = new ImageHandlerParameters(
            $request->getMethod(),
            $this->detectedImageSource
        );

        return $this->requestHandlerFactory->createRequestHandlerByParameters($parameters);
    }

    private function transformRequestForDetectedImageSource(RequestInterface $request): RequestInterface
    {
        return $this->requestTransformer->transformRequestForImageSource($request, $this->detectedImageSource);
    }
}
