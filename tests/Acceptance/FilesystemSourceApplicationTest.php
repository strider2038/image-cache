<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Acceptance;

use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Tests\Support\AcceptanceTestCase;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FilesystemSourceApplicationTest extends AcceptanceTestCase
{
    private const URL_NOT_EXIST = '/i/not-exist.jpg';
    private const URL_INCORRECT_EXTENSION = '/index.php';
    private const URL_IMAGE_JPEG = '/sub/dir/image.jpg';

    /** @test */
    public function GET_givenUrlWithIncorrectExtension_404NotFound(): void
    {
        $response = $this->sendGET(self::URL_INCORRECT_EXTENSION);

        $this->assertEquals(HttpStatusCodeEnum::NOT_FOUND, $response->getStatusCode());
    }

    /** @test */
    public function GET_imageDoesNotExist_404NotFound(): void
    {
        $response = $this->sendGET(self::URL_NOT_EXIST);

        $this->assertEquals(HttpStatusCodeEnum::NOT_FOUND, $response->getStatusCode());
    }

    /** @test */
    public function POST_invalidImageBody_400BadRequest(): void
    {
        $response = $this->sendPOST(self::URL_IMAGE_JPEG);

        $this->assertEquals(HttpStatusCodeEnum::BAD_REQUEST, $response->getStatusCode());
    }
}
