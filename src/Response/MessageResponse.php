<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Response;

use Strider2038\ImgCache\Core\DeprecatedResponse;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class MessageResponse extends DeprecatedResponse
{
    /** @var string */
    private $message;

    public function __construct(int $httpCode = null, string $message = null)
    {
        parent::__construct($httpCode ?? self::HTTP_CODE_OK);
        $this->message = $message;
    }

    protected function sendContent(): void
    {
        echo $this->message;
    }
}