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

use Psr\Container\ContainerInterface;
use Strider2038\ImgCache\Core\Service\SequentialServiceRunner;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class TestingSequentialServiceRunner extends SequentialServiceRunner
{
    /** @var \Closure */
    private $containerModifierCallable;

    public function __construct(\Closure $containerModifierCallable)
    {
        $this->containerModifierCallable = $containerModifierCallable;
    }

    public function runServices(ContainerInterface $container): void
    {
        \call_user_func($this->containerModifierCallable, $container);

        parent::runServices($container);
    }
}
