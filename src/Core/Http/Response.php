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

use Strider2038\ImgCache\Enum\HttpStatusCode;


/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class Response extends Message implements ResponseInterface
{
    /** @var HttpStatusCode */
    private $statusCode;

    public function __construct(HttpStatusCode $statusCode)
    {
        $this->statusCode = $statusCode;
        parent::__construct();
    }

    public function getStatusCode(): HttpStatusCode
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
            HttpStatusCode::OK                     => 'OK',
            HttpStatusCode::CREATED                => 'Created',
            HttpStatusCode::ACCEPTED               => 'Accepted',
            HttpStatusCode::RESET_CONTENT          => 'Reset Content',
            HttpStatusCode::MOVED_PERMANENTLY      => 'Moved Permanently',
            HttpStatusCode::FOUND                  => 'Found',
            HttpStatusCode::NOT_MODIFIED           => 'Not Modified',
            HttpStatusCode::BAD_REQUEST            => 'Bad Request',
            HttpStatusCode::UNAUTHORIZED           => 'Unauthorized',
            HttpStatusCode::FORBIDDEN              => 'Forbidden',
            HttpStatusCode::NOT_FOUND              => 'Not Found',
            HttpStatusCode::METHOD_NOT_ALLOWED     => 'Method Not Allowed',
            HttpStatusCode::UNSUPPORTED_MEDIA_TYPE => 'Unsupported Media Type',
            HttpStatusCode::INTERNAL_SERVER_ERROR  => 'Internal Server Error',
        ];
    }
}