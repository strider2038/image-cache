<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Service\Routing;

use Strider2038\ImgCache\Core\EntityInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class RoutingPath implements EntityInterface
{
    /**
     * @Assert\NotBlank()
     * @Assert\NotIdenticalTo("/")
     * @Assert\Regex(
     *     pattern="/^\/([A-Z0-9_]+(\/){0,1})*[^\/|\s]$/i",
     *     message="Value must contain only latin symbols, digits, snakes '_' and single slashes '/'"
     * )
     * @var string
     */
    private $urlPrefix;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/[A-Z]+[A-Z0-9]{0,}/i",
     *     message="Value must contain only latin symbols and digits and start from latin symbol"
     * )
     * @var string
     */
    private $controllerId;

    public function __construct(string $urlPrefix, string $controllerId)
    {
        $this->urlPrefix = $urlPrefix;
        $this->controllerId = $controllerId;
    }

    public function getUrlPrefix(): string
    {
        return $this->urlPrefix;
    }

    public function getControllerId(): string
    {
        return $this->controllerId;
    }
}
