<?php

namespace Strider2038\ImgCache\Core;

/**
 *
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface ControllerInterface {
    public function runAction(string $action, RequestInterface $request): Response;
}
