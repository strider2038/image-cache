<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit;

use Strider2038\ImgCache\ApplicationFactory;
use Strider2038\ImgCache\Core\Application;
use Strider2038\ImgCache\Core\ApplicationParameters;
use Strider2038\ImgCache\Tests\Support\FileTestCase;

class ApplicationFactoryTest extends FileTestCase
{
    /** @test */
    public function createApplication_givenApplicationParameters_applicationCreatedAndReturned(): void
    {
        $parameters = new ApplicationParameters(
            self::TEST_CACHE_DIR,
            []
        );

        $application = ApplicationFactory::createApplication($parameters);

        $this->assertInstanceOf(Application::class, $application);
    }
}
