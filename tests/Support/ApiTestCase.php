<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Support;

use GuzzleHttp\Client;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ApiTestCase extends FileTestCase
{
    /** @var \GuzzleHttp\Client */
    protected $client;
    
    protected function setUp()
    {
        parent::setUp();
        $this->client = new Client([
            'base_uri' => 'http://localhost:8080/',
            'timeout' => 5,
            'allow_redirects' => false,
            'http_errors' => false,
        ]);
        
        exec('rm -rf ' . static::getTestWebDir());
        mkdir(static::getTestWebDir());
    }

    public static function getTestWebDir(): string
    {
        return __DIR__ . '/../../web/i/';
    }
    
    /**
     * @param string $urlPrefix
     * @param string $name
     * @return string[] imageFilename, imageUrl
     */
    public function havePublicImage(string $urlPrefix = '', string $name = self::IMAGE_CAT300): array
    {
        $filename = static::getTestWebDir() . $urlPrefix . $name;
        copy(TestImages::getFilename($name), $filename);
        return [$filename, '/i/' . $name];
    }
}
