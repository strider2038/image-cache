<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Configuration\ImageSource;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Configuration\ImageSource\WebDAVImageSource;
use Strider2038\ImgCache\Utility\EntityValidator;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;
use Strider2038\ImgCache\Utility\MetadataReader;
use Strider2038\ImgCache\Utility\Validation\CustomConstraintValidatorFactory;
use Strider2038\ImgCache\Utility\ViolationFormatter;

class WebDAVImageSourceTest extends TestCase
{
    private const CACHE_DIRECTORY = '/cache_directory';
    private const STORAGE_DIRECTORY = 'storage_directory';
    private const PROCESSOR_TYPE = 'copy';
    private const DRIVER_URI = 'driver_uri';
    private const OAUTH_TOKEN = 'oauth_token';
    private const IMAGE_STORAGE_SERVICE_ID = 'filesystem_storage';
    private const ENTITY_ID = 'WebDAV image source';

    /** @var EntityValidatorInterface */
    private $validator;

    protected function setUp(): void
    {
        $this->validator = new EntityValidator(
            new CustomConstraintValidatorFactory(
                new MetadataReader()
            ),
            new ViolationFormatter()
        );
    }

    /** @test */
    public function construct_givenParameters_parametersSetAndAccessible(): void
    {
        $source = new WebDAVImageSource(
            self::CACHE_DIRECTORY,
            self::STORAGE_DIRECTORY,
            self::PROCESSOR_TYPE,
            self::DRIVER_URI,
            self::OAUTH_TOKEN
        );

        $this->assertEquals(self::CACHE_DIRECTORY, $source->getCacheDirectory());
        $this->assertEquals(self::STORAGE_DIRECTORY, $source->getStorageDirectory());
        $this->assertEquals(self::PROCESSOR_TYPE, $source->getProcessorType());
        $this->assertEquals(self::DRIVER_URI, $source->getDriverUri());
        $this->assertEquals(self::OAUTH_TOKEN, $source->getOauthToken());
        $this->assertEquals(self::IMAGE_STORAGE_SERVICE_ID, $source->getImageStorageServiceId());
        $this->assertEquals(self::ENTITY_ID, $source->getId());
    }

    /**
     * @test
     * @param string $driverUri
     * @param string $oauthToken
     * @param int $violationsCount
     * @dataProvider imageSourceParametersProvider
     */
    public function validate_givenParameters_violationsReturned(
        string $driverUri,
        string $oauthToken,
        int $violationsCount
    ): void {
        $imageSource = new WebDAVImageSource(
            self::CACHE_DIRECTORY,
            self::STORAGE_DIRECTORY,
            self::PROCESSOR_TYPE,
            $driverUri,
            $oauthToken
        );

        $violations = $this->validator->validate($imageSource);

        $this->assertCount($violationsCount, $violations);
    }

    public function imageSourceParametersProvider(): array
    {
        return [
            ['http://example.com', self::OAUTH_TOKEN, 0],
            ['', '', 2],
            ['not-uri', self::OAUTH_TOKEN, 1],
        ];
    }
}
