<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Core;

use Strider2038\ImgCache\Application;
use Strider2038\ImgCache\Exception\ApplicationException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ComponentsContainer extends Component 
{
    /** @var array */
    private $components;
    
    public function __construct(Application $app, array $components = []) 
    {
        parent::__construct($app);
        foreach ($components as $name => $component) {
            $this->set($name, $component);
        }
    }
    
    /**
     * @param string $name
     * @param \Strider2038\ImgCache\Core\Component|callable $component
     * @return $this
     * @throws ApplicationException
     */
    public function set(string $name, $component) 
    {
        if (isset($this->components[$name])) {
            throw new ApplicationException("Component '{$name}' already exists");
        }
        if (!is_callable($component) && !is_object($component)) {
            throw new ApplicationException(
                "Component '{$name}' must be a callable or an object."
            );
        }
        $this->components[$name] = $component;
        return $this;
    }
    
    public function get($name)
    {
        if (!isset($this->components[$name])) {
            throw new ApplicationException("Component '{$name}' not found");
        }
        if (!is_object($this->components[$name])) {
            throw new ApplicationException("Cannot create component '{$name}'");
        }
        if (!is_callable($this->components[$name])) {
            return $this->components[$name];
        }
        
        // component construction via callable function
        $obj = $this->components[$name]($this->getApp());
        if (!is_object($obj) || is_callable($obj)) {
            throw new ApplicationException(
                "Incorrect instance of object '{$name}'. It must be an"
                . " object and cannot be callable."
            );
        }
        return $this->components[$name] = $obj;
    }
}
