<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Functional\Api;

use Strider2038\ImgCache\Tests\Support\ApiTestCase;
use Strider2038\ImgCache\Core\Response;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageCacheApiTest extends ApiTestCase
{

    public function testGet_ImageDoesNotExist_Http404Returned(): void
    {
        /** @var \GuzzleHttp\Psr7\Response */
        $response = $this->client->request('GET', '/i/' . self::IMAGE_CAT300);
        
        $this->assertEquals(Response::HTTP_CODE_NOT_FOUND, $response->getStatusCode());
    }
    
    public function testGet_ImageExist_Http200Returned(): void
    {
        [$imageFilename, $imageUrl] = $this->havePublicImage();
        
        /** @var \GuzzleHttp\Psr7\Response */
        $response = $this->client->request('GET', $imageUrl);
        
        $this->assertFileExists($imageFilename);
        $this->assertEquals(Response::HTTP_CODE_OK, $response->getStatusCode());
        $this->assertEquals(
            Response::CONTENT_TYPE_IMAGE_JPEG, 
            $response->getHeader(Response::HTTP_HEADER_CONTENT_TYPE)[0]
        );
    }
    
}
