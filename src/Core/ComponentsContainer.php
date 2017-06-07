<?php

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
        $this->setApp($app);
        $this->components = $components;
    }
    
    public function get($name) 
    {
        if (!isset($this->components[$name])) {
            throw new ApplicationException("Component '{$name}' not found");
        }
        if ($this->components[$name] instanceof Component) {
            return $this->components[$name];
        }
        if (is_callable($this->components[$name])) {
            $obj = $this->components[$name]();
            if (!$obj instanceof Component) {
                throw new ApplicationException(
                    "Component '{$name}' must be instance of Strider2038\ImgCache\Core\Object"
                );
            }
            $obj->setApp($this->getApp());
            return $this->components[$name] = $obj;
        }
        throw new ApplicationException("Cannot create component '{$name}'");
    }
}
