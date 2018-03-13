<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\NullErrorHandler;

class NullErrorHandlerTest extends TestCase
{
    /** @test */
    public function register_noParameters_nothingExpected(): void
    {
        $handler = new NullErrorHandler();

        $handler->register();

        $this->assertNotNull($handler);
    }
}
