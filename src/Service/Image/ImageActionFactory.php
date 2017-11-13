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
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\ImageCacheInterface;
use Strider2038\ImgCache\Imaging\ImageStorageInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageActionFactory implements ActionFactoryInterface
{
    private const ACTION_ID_GET = 'get';
    private const ACTION_ID_CREATE = 'create';
    private const ACTION_ID_REPLACE = 'replace';
    private const ACTION_ID_DELETE = 'delete';

    /** @var ImageStorageInterface */
    private $imageStorage;

    /** @var ImageCacheInterface */
    private $imageCache;

    /** @var ImageFactoryInterface */
    private $imageFactory;

    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var RequestInterface */
    private $request;

    public function __construct(
        ImageStorageInterface $imageStorage,
        ImageCacheInterface $imageCache,
        ImageFactoryInterface $imageFactory,
        ResponseFactoryInterface $responseFactory,
        RequestInterface $request
    ) {
        $this->imageStorage = $imageStorage;
        $this->imageCache = $imageCache;
        $this->imageFactory = $imageFactory;
        $this->responseFactory = $responseFactory;
        $this->request = $request;
    }

    public function createAction(string $actionId, string $location): ActionInterface
    {
        $action = null;

        if ($actionId === self::ACTION_ID_GET) {
            $action = new GetAction($location, $this->responseFactory, $this->imageStorage, $this->imageCache);
        } elseif ($actionId === self::ACTION_ID_CREATE) {
            $action = new CreateAction($location, $this->responseFactory, $this->imageStorage, $this->imageFactory, $this->request);
        } elseif ($actionId === self::ACTION_ID_REPLACE) {
            $action = new ReplaceAction($location, $this->responseFactory, $this->imageStorage, $this->imageCache, $this->imageFactory, $this->request);
        } elseif ($actionId === self::ACTION_ID_DELETE) {
            $action = new DeleteAction($location, $this->responseFactory, $this->imageStorage, $this->imageCache);
        }

        if ($action === null) {
            throw new InvalidRouteException(sprintf('Action "%s" not found', $actionId));
        }

        return $action;
    }
}
