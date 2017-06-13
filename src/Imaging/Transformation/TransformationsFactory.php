<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Transformation;

use Strider2038\ImgCache\Application;
use Strider2038\ImgCache\Core\Component;
use Strider2038\ImgCache\Exception\InvalidConfigException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class TransformationsFactory extends Component implements TransformationsFactoryInterface
{
    /** @var array */
    private $constructors = [];

    public function __construct(Application $app, array $constructors = null)
    {
        parent::__construct($app);
        $this->constructors = array_replace(
            $this->getDefaultConstructors(), 
            !empty($constructors) ? $constructors : []
        );
    }
    
    public function create(string $config): TransformationInterface
    {
        $index1 = substr($config, 0, 1);
        $index2 = substr($config, 0, 2);
        if (isset($this->constructors[$index1])) {
            return $this->constructors[$index1](substr($config, 1));
        } elseif (isset ($this->constructors[$index2])) {
            return $this->constructors[$index2](substr($config, 2));
        }
        throw new InvalidConfigException("Cannot create transformation for '{$config}'");
    }
    
    public function getDefaultConstructors(): array
    {
        return [
            'q' => function(string $config): TransformationInterface {
                return new Quality((int) $config);
            }
        ];
    }
}
