<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Source\Yandex;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use Strider2038\ImgCache\Core\QueryParameter;
use Strider2038\ImgCache\Core\QueryParametersCollection;
use Strider2038\ImgCache\Enum\HttpMethodEnum;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Exception\BadApiResponse;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Image\ImageInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class YandexMapSource implements YandexMapSourceInterface
{
    /** @var ImageFactoryInterface */
    private $imageFactory;

    /** @var ClientInterface */
    private $client;

    /** @var string */
    private $key;

    public function __construct(
        ImageFactoryInterface $imageFactory,
        ClientInterface $client,
        string $key = ''
    ) {
        $this->imageFactory = $imageFactory;
        $this->client = $client;
        $this->key = $key;
    }

    public function get(QueryParametersCollection $queryParameters): ImageInterface
    {
        if ($this->key !== '') {
            $queryParameters = clone $queryParameters;
            $queryParameters->add(new QueryParameter('key', $this->key));
        }

        try {
            $response = $this->client->request(HttpMethodEnum::GET, '', [
                RequestOptions::QUERY => $queryParameters->toArray()
            ]);
        } catch (\Exception $exception) {
            throw new BadApiResponse(
                'Unexpected response from API',
                HttpStatusCodeEnum::BAD_GATEWAY,
                $exception
            );
        }

        if ($response->getStatusCode() !== HttpStatusCodeEnum::OK) {
            throw new BadApiResponse('Unexpected response from API');
        }

        $body = $response->getBody();
        $data = $body->getContents();

        return $this->imageFactory->createImageBlob($data);
    }
}
