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
use Strider2038\ImgCache\Configuration\ImageSource\GeoMapImageSource;
use Strider2038\ImgCache\Utility\EntityValidator;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;
use Strider2038\ImgCache\Utility\MetadataReader;
use Strider2038\ImgCache\Utility\Validation\CustomConstraintValidatorFactory;
use Strider2038\ImgCache\Utility\ViolationFormatter;

class GeoMapImageSourceTest extends TestCase
{
    private const CACHE_DIRECTORY = '/cache_directory/';
    private const IMAGE_STORAGE_SERVICE_ID = 'geo_map_storage';
    private const DRIVER = 'driver';
    private const API_KEY = 'api_key';
    private const ENTITY_ID = 'geographical map image source';

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
        $source = new GeoMapImageSource(
            self::CACHE_DIRECTORY,
            self::DRIVER,
            self::API_KEY
        );

        $this->assertEquals(self::CACHE_DIRECTORY, $source->getCacheDirectory());
        $this->assertEquals(self::DRIVER, $source->getDriver());
        $this->assertEquals(self::API_KEY, $source->getApiKey());
        $this->assertEquals(self::IMAGE_STORAGE_SERVICE_ID, $source->getImageStorageServiceId());
        $this->assertEquals(self::ENTITY_ID, $source->getId());
    }

    /**
     * @test
     * @param string $driver
     * @param string $apiKey
     * @param int $violationsCount
     * @dataProvider imageSourceParametersProvider
     */
    public function validate_givenParameters_violationsReturned(
        string $driver,
        string $apiKey,
        int $violationsCount
    ): void {
        $imageSource = new GeoMapImageSource(
            self::CACHE_DIRECTORY,
            $driver,
            $apiKey
        );

        $violations = $this->validator->validate($imageSource);

        $this->assertCount($violationsCount, $violations);
    }

    public function imageSourceParametersProvider(): array
    {
        return [
            ['', '', 1],
            ['yandex', 'key', 0],
        ];
    }
}
