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
use Strider2038\ImgCache\Core\NullAction;
use Strider2038\ImgCache\Exception\InvalidRouteException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageActionFactory implements ActionFactoryInterface
{
    private const ACTION_ID_GET = 'get';
    private const ACTION_ID_CREATE = 'create';
    private const ACTION_ID_REPLACE = 'replace';
    private const ACTION_ID_DELETE = 'delete';

    /** @var ActionInterface */
    private $getAction;

    /** @var ActionInterface */
    private $createAction;

    /** @var ActionInterface */
    private $replaceAction;

    /** @var ActionInterface */
    private $deleteAction;

    public function __construct(
        GetAction $getAction,
        CreateAction $createAction = null,
        ReplaceAction $replaceAction = null,
        DeleteAction $deleteAction = null
    ) {
        $this->getAction = $getAction;
        $this->createAction = $createAction ?? new NullAction();
        $this->replaceAction = $replaceAction ?? new NullAction();
        $this->deleteAction = $deleteAction ?? new NullAction();
    }

    public function createAction(string $actionId): ActionInterface
    {
        $map = $this->getActionsMap();

        if (array_key_exists($actionId, $map)) {
            $action = $map[$actionId];
        } else {
            throw new InvalidRouteException(sprintf('Action "%s" not found', $actionId));
        }

        return $action;
    }

    private function getActionsMap(): array
    {
        return [
            self::ACTION_ID_GET => $this->getAction,
            self::ACTION_ID_CREATE => $this->createAction,
            self::ACTION_ID_REPLACE => $this->replaceAction,
            self::ACTION_ID_DELETE => $this->deleteAction
        ];
    }
}
