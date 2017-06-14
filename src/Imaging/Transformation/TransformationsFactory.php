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
                if (!preg_match('/^\d+$/', $config)) {
                    throw new InvalidConfigException('Invalid config for quality transformation');
                }
                return new Quality((int) $config);
            },
            's' => function(string $config): TransformationInterface {
                $isValid = preg_match(
                    '/^(\d+)(x(\d+)){0,1}([fswh]{1}){0,1}$/', 
                    strtolower($config), 
                    $matches
                );
                if (!$isValid) {
                    throw new InvalidConfigException('Invalid config for resize transformation');
                }
                $width = $matches[1] ?? 0;
                $heigth = !empty($matches[3]) ? (int) $matches[3] : null;
                $mode = Resize::MODE_STRETCH;
                $modeCode = $matches[4] ?? null;
                switch ($modeCode) {
                    case 'f': $mode = Resize::MODE_FIT_IN; break;
                    case 's': $mode = Resize::MODE_STRETCH; break;
                    case 'w': $mode = Resize::MODE_PRESERVE_WIDTH; break;
                    case 'h': $mode = Resize::MODE_PRESERVE_HEIGHT; break;
                }
                return new Resize($width, $heigth, $mode);
            },
        ];
    }
}
