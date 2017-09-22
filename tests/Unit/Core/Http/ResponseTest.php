<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Core\Http;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\Http\Response;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;

class ResponseTest extends TestCase
{
    /** @test */
    public function construct_givenStatusCode_statusCodeIsSetAndValidReasonPhraseIsReturned(): void
    {
        $statusCode = new HttpStatusCodeEnum(HttpStatusCodeEnum::OK);

        $response = new Response($statusCode);

        $this->assertEquals(HttpStatusCodeEnum::OK, $response->getStatusCode()->getValue());
        $this->assertEquals('OK', $response->getReasonPhrase());
    }
}
