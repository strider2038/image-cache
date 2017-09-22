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

use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Response extends Message implements ResponseInterface
{
    /** @var HttpStatusCodeEnum */
    private $statusCode;

    public function __construct(HttpStatusCodeEnum $statusCode)
    {
        $this->statusCode = $statusCode;
        parent::__construct();
    }

    public function getStatusCode(): HttpStatusCodeEnum
    {
        return $this->statusCode;
    }

    public function getReasonPhrase(): string
    {
        return $this->getReasonPhrasesMap()[$this->statusCode->getValue()];
    }

    public function getReasonPhrasesMap(): array
    {
        return [
            HttpStatusCodeEnum::OK                     => 'OK',
            HttpStatusCodeEnum::CREATED                => 'Created',
            HttpStatusCodeEnum::ACCEPTED               => 'Accepted',
            HttpStatusCodeEnum::RESET_CONTENT          => 'Reset Content',
            HttpStatusCodeEnum::MOVED_PERMANENTLY      => 'Moved Permanently',
            HttpStatusCodeEnum::FOUND                  => 'Found',
            HttpStatusCodeEnum::NOT_MODIFIED           => 'Not Modified',
            HttpStatusCodeEnum::BAD_REQUEST            => 'Bad Request',
            HttpStatusCodeEnum::UNAUTHORIZED           => 'Unauthorized',
            HttpStatusCodeEnum::FORBIDDEN              => 'Forbidden',
            HttpStatusCodeEnum::NOT_FOUND              => 'Not Found',
            HttpStatusCodeEnum::METHOD_NOT_ALLOWED     => 'Method Not Allowed',
            HttpStatusCodeEnum::UNSUPPORTED_MEDIA_TYPE => 'Unsupported Media Type',
            HttpStatusCodeEnum::INTERNAL_SERVER_ERROR  => 'Internal Server Error',
        ];
    }
}
