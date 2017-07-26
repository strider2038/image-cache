<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Processing;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;
use Strider2038\ImgCache\Imaging\Processing\SaveOptionsFactory;

class SaveOptionsFactoryTest extends TestCase
{
    public function testCreate_Nop_SaveOptionsIsReturned(): void
    {
        $saveOptionsFactory = new SaveOptionsFactory();

        $saveOptions = $saveOptionsFactory->create();

        $this->assertInstanceOf(SaveOptions::class, $saveOptions);
    }
}
