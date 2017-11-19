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

    /**
     * @test
     * @dataProvider statusCodeProvider
     * @param int $code
     */
    public function construct_givenStatusCode_responseHasReasonPhrase(int $code): void
    {
        $statusCode = new HttpStatusCodeEnum($code);

        $response = new Response($statusCode);

        $this->assertEquals($code, $response->getStatusCode()->getValue());
        $this->assertGreaterThan(0, strlen($response->getReasonPhrase()));
    }

    public function statusCodeProvider(): array
    {
        $items = [];
        $codes = HttpStatusCodeEnum::values();

        foreach ($codes as $code) {
            /** @var HttpStatusCodeEnum $code */
            $items[] = [$code->getValue()];
        }

        return $items;
    }
}
