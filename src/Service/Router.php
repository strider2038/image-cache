<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Service;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Route;
use Strider2038\ImgCache\Core\RouterInterface;
use Strider2038\ImgCache\Enum\HttpMethodEnum;
use Strider2038\ImgCache\Exception\InvalidRequestException;
use Strider2038\ImgCache\Exception\InvalidRouteException;
use Strider2038\ImgCache\Imaging\Validation\ImageValidatorInterface;
use Strider2038\ImgCache\Service\Routing\UrlRouteDetectorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Router implements RouterInterface
{
    /** @var ImageValidatorInterface */
    private $imageValidator;

    /** @var UrlRouteDetectorInterface */
    private $urlRouteDetector;

    /** @var LoggerInterface */
    private $logger;

    private $methodsToActionsMap = [
        HttpMethodEnum::GET    => 'get',
        HttpMethodEnum::POST   => 'create',
        HttpMethodEnum::PUT    => 'replace',
        HttpMethodEnum::DELETE => 'delete',
    ];

    public function __construct(
        ImageValidatorInterface $imageValidator,
        UrlRouteDetectorInterface $urlRouteDetector = null
    ) {
        $this->imageValidator = $imageValidator;
        $this->urlRouteDetector = $urlRouteDetector ?? new ImageDefaultRouteDetector();
        $this->logger = new NullLogger();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function getRoute(RequestInterface $request): Route
    {
        $requestMethod = $request->getMethod()->getValue();
        $url = $request->getUri()->getPath();

        $this->logger->info(sprintf('Processing route for request %s: %s', $requestMethod, $url));

        if (!array_key_exists($requestMethod, $this->methodsToActionsMap)) {
            throw new InvalidRouteException('Route not found');
        }

        if (!$this->imageValidator->hasValidImageExtension($url)) {
            throw new InvalidRequestException('Requested file has incorrect extension');
        }

        $urlRoute = $this->urlRouteDetector->getUrlRoute($request);

        $controllerId = $urlRoute->getControllerId();
        $actionId = $this->methodsToActionsMap[$requestMethod];
        $processedRequest = $request->withUri($urlRoute->getUri());

        $route = new Route($controllerId, $actionId, $processedRequest);

        $this->logger->info(sprintf(
            'Route is detected: controller id = %s, action id = %s, request url = %s',
            $route->getControllerId(),
            $route->getActionId(),
            $route->getRequest()->getUri()
        ));

        return $route;
    }
}
