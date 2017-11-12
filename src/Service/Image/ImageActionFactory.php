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

use Strider2038\ImgCache\Core\ActionFactoryInterface;
use Strider2038\ImgCache\Core\ActionInterface;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseFactoryInterface;
use Strider2038\ImgCache\Exception\InvalidRouteException;
use Strider2038\ImgCache\Imaging\DeprecatedImageCacheInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageActionFactory implements ActionFactoryInterface
{
    private const ACTION_ID_GET = 'get';
    private const ACTION_ID_CREATE = 'create';
    private const ACTION_ID_REPLACE = 'replace';
    private const ACTION_ID_DELETE = 'delete';

    /** @var DeprecatedImageCacheInterface */
    private $imageCache;

    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var RequestInterface */
    private $request;

    public function __construct(
        DeprecatedImageCacheInterface $imageCache,
        ResponseFactoryInterface $responseFactory,
        RequestInterface $request
    ) {
        $this->imageCache = $imageCache;
        $this->responseFactory = $responseFactory;
        $this->request = $request;
    }

    public function createAction(string $actionId, string $location): ActionInterface
    {
        $action = null;

        if ($actionId === self::ACTION_ID_GET) {
            $action = new GetAction($location, $this->imageCache, $this->responseFactory);
        } elseif ($actionId === self::ACTION_ID_CREATE) {
            $action = new CreateAction($location, $this->imageCache, $this->responseFactory, $this->request);
        } elseif ($actionId === self::ACTION_ID_REPLACE) {
            $action = new ReplaceAction($location, $this->imageCache, $this->responseFactory, $this->request);
        } elseif ($actionId === self::ACTION_ID_DELETE) {
            $action = new DeleteAction($location, $this->imageCache, $this->responseFactory);
        }

        if ($action === null) {
            throw new InvalidRouteException(sprintf('Action "%s" not found', $actionId));
        }

        return $action;
    }
}
