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
use Strider2038\ImgCache\Configuration\ImageSource\FilesystemImageSource;
use Strider2038\ImgCache\Configuration\ImageSource\GeoMapImageSource;
use Strider2038\ImgCache\Configuration\ImageSource\ImageSourceFactory;
use Strider2038\ImgCache\Configuration\ImageSource\WebDAVImageSource;
use Strider2038\ImgCache\Exception\InvalidConfigurationException;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

class ImageSourceFactoryTest extends TestCase
{
    private const CACHE_DIRECTORY = 'cache-directory';

    /** @var EntityValidatorInterface */
    private $validator;

    protected function setUp(): void
    {
        $this->validator = \Phake::mock(EntityValidatorInterface::class);
    }

    /**
     * @test
     * @param array $configurationArray
     * @param string $imageSourceClass
     * @dataProvider configurationAndImageSourceClassProvider
     */
    public function createImageSourceByConfiguration_givenConfigurationArray_concreteImageSourceCreatedAndReturned(
        array $configurationArray,
        string $imageSourceClass
    ): void {
        $factory = new ImageSourceFactory($this->validator);

        $imageSource = $factory->createImageSourceByConfiguration($configurationArray);

        $this->assertInstanceOf($imageSourceClass, $imageSource);
        $this->assertEquals(self::CACHE_DIRECTORY, $imageSource->getCacheDirectory());
        $this->assertValidator_validateWithException_isCalledOnceWithEntityClassAndExceptionClass(
            $imageSourceClass,
            InvalidConfigurationException::class
        );
    }

    public function configurationAndImageSourceClassProvider(): array
    {
        return [
            [
                [
                    'type' => 'filesystem',
                    'cache_directory' => self::CACHE_DIRECTORY,
                    'storage_directory' => '',
                    'processor_type' => '',
                ],
                FilesystemImageSource::class,
            ],
            [
                [
                    'type' => 'webdav',
                    'cache_directory' => self::CACHE_DIRECTORY,
                    'storage_directory' => '',
                    'processor_type' => '',
                    'driver_uri' => '',
                    'oauth_token' => '',
                ],
                WebDAVImageSource::class,
            ],
            [
                [
                    'type' => 'geomap',
                    'cache_directory' => self::CACHE_DIRECTORY,
                    'driver' => '',
                    'api_key' => '',
                ],
                GeoMapImageSource::class,
            ],
        ];
    }

    private function assertValidator_validateWithException_isCalledOnceWithEntityClassAndExceptionClass(
        string $entityClass,
        string $exceptionClass
    ): void {
        \Phake::verify($this->validator, \Phake::times(1))
            ->validateWithException(\Phake::capture($entity), \Phake::capture($exception));
        $this->assertInstanceOf($entityClass, $entity);
        $this->assertEquals($exceptionClass, $exception);
    }
}
