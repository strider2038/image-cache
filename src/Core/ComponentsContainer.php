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
            throw new ApplicationException("Component '{$name}' is already exists");
        }
        if (!is_callable($component) && !$component instanceof Component) {
            throw new ApplicationException(
                "Component '{$name}' must be a callable "
                . "or an instance of " . Component::class
            );
        }
        $this->components[$name] = $component;
        return $this;
    }
    
    public function get($name): Component
    {
        if (!isset($this->components[$name])) {
            throw new ApplicationException("Component '{$name}' not found");
        }
        if ($this->components[$name] instanceof Component) {
            return $this->components[$name];
        }
        if (is_callable($this->components[$name])) {
            $obj = $this->components[$name]($this->getApp());
            if (!$obj instanceof Component) {
                throw new ApplicationException(
                    "Component '{$name}' must be instance of " . Component::class
                );
            }
            return $this->components[$name] = $obj;
        }
        throw new ApplicationException("Cannot create component '{$name}'");
    }
}
