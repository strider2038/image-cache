<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Response;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\DeprecatedResponse;
use Strider2038\ImgCache\Response\MessageResponse;

class MessageResponseTest extends TestCase
{
    const MESSAGE = 'message';

    /**
     * @runInSeparateProcess
     * @group separate
     */
    public function testSend_MessageIsSet_MessageIsEchoed(): void
    {
        $response = new MessageResponse(DeprecatedResponse::HTTP_CODE_OK, self::MESSAGE);
        $this->expectOutputString(self::MESSAGE);

        $response->send();
    }
}
