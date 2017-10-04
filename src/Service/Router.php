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
use Strider2038\ImgCache\Exception\InvalidConfigurationException;
use Strider2038\ImgCache\Exception\InvalidRequestException;
use Strider2038\ImgCache\Exception\InvalidRouteException;
use Strider2038\ImgCache\Imaging\Validation\ImageValidatorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Router implements RouterInterface
{
    private const DEFAULT_CONTROLLER_ID = 'imageController';

    /** @var ImageValidatorInterface */
    private $imageValidator;

    /** @var string[] */
    private $urlMaskToControllersMap;

    /** @var LoggerInterface */
    private $logger;

    private $methodsToActionsMap = [
        HttpMethodEnum::GET    => 'get',
        HttpMethodEnum::POST   => 'create',
        HttpMethodEnum::PUT    => 'replace',
        HttpMethodEnum::DELETE => 'delete',
    ];

    public function __construct(ImageValidatorInterface $imageValidator, array $urlMaskToControllersMap = [])
    {
        $this->imageValidator = $imageValidator;
        $this->urlMaskToControllersMap = [];
        foreach ($urlMaskToControllersMap as $prefix => $controllerId) {
            $this->validateUrlMapRow($prefix, $controllerId);
            $this->urlMaskToControllersMap[str_replace('/', '\/', $prefix)] = $controllerId;
        }
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

        $this->logger->info("Processing route for request {$requestMethod} {$url}");

        if (!array_key_exists($requestMethod, $this->methodsToActionsMap)) {
            throw new InvalidRouteException('Route not found');
        }

        if (!$this->imageValidator->hasValidImageExtension($url)) {
            throw new InvalidRequestException('Requested file has incorrect extension');
        }

        [$controllerId, $location] = $this->splitUrlToControllerAndLocation($url);
        $route = new Route($controllerId, $this->methodsToActionsMap[$requestMethod], $location);

        $this->logger->info(sprintf(
            'Route is detected: controller id = %s, action id = %s, location = %s',
            $route->getControllerId(),
            $route->getActionId(),
            $route->getLocation()
        ));

        return $route;
    }

    private function splitUrlToControllerAndLocation(string $url): array
    {
        if (count($this->urlMaskToControllersMap) <= 0) {
            return [self::DEFAULT_CONTROLLER_ID, $url];
        }

        foreach ($this->urlMaskToControllersMap as $prefix => $controllerId) {
            if (preg_match_all('/^' . $prefix . '\/(?<url>.*)$/i', $url, $matches)) {
                if (!empty($matches['url'][0])) {
                    return [$controllerId, '/' . $matches['url'][0]];
                }
            }
        }

        throw new InvalidRouteException('Route not found');
    }

    private function validateUrlMapRow(string $prefix, string $controllerId): void
    {
        if (empty($prefix) || $prefix === '/') {
            throw new InvalidConfigurationException(
                "Url mask to controllers map is invalid: prefix cannot be empty or slash"
            );
        }
        if (!preg_match('/^\/([A-Z0-9_]+(\/){0,1})*[^\/|\s]$/i', $prefix)) {
            throw new InvalidConfigurationException(
                "Url mask to controllers map is invalid: incorrect prefix '{$prefix}'"
            );
        }
        if (!preg_match('/[A-Z]+[A-Z0-9]{0,}/i', $controllerId)) {
            throw new InvalidConfigurationException(
                "Url mask to controllers map is invalid: controller id can contain only latin characters and digits"
            );
        }
    }
}
