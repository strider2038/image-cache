<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Acceptance\Api;

use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Tests\Support\ApiTestCase;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageCacheApiTest extends ApiTestCase
{
    const URL_NOT_EXIST = '/i/not-exist.jpg';
    const URL_INCORRECT_EXTENSION = '/index.php';

    public function testGet_GivenUrlWithIncorrectExtension_400BadRequestIsReturned(): void
    {
        /** @var \GuzzleHttp\Psr7\Response */
        $response = $this->client->request('GET', self::URL_INCORRECT_EXTENSION);

        $this->assertEquals(HttpStatusCodeEnum::BAD_REQUEST, $response->getStatusCode());
    }

    public function testGet_ImageDoesNotExist_404NotFoundIsReturned(): void
    {
        /** @var \GuzzleHttp\Psr7\Response */
        $response = $this->client->request('GET', self::URL_NOT_EXIST);

        $this->assertEquals(HttpStatusCodeEnum::NOT_FOUND, $response->getStatusCode());
    }
}
