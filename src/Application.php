<?php

namespace Strider2038\ImgCache;

use Strider2038\ImgCache\Exception\ApplicationException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 * @property Core\Request $request Web request object
 */
class Application
{
    /** @var string */
    private $id;

    /** @var Core\ComponentsContainer */
    private $container;

    /**
     * @param array $config
     * @throws ApplicationException
     */
    public function __construct(array $config = []) {
        if (!isset($config['id']) || !is_string($config['id'])) {
            throw new ApplicationException('Empty application id');
        }
        $this->id = $config['id'];
        
        $components = array_merge(
            $config['components'] ?? [], 
            $this->getCoreComponents()
        );
        
        $this->container = new Core\ComponentsContainer(
            $this,
            $components
        );
    }
    
    public function __get($name) {
        return $this->container->get($name);
    }
    
    /**
     * @return string
     */
    public function getId(): string {
        return $this->id;
    }
    
    public function run(): void {
        echo "ID: $this->id<br>";
        /** @var \Strider2038\ImgCache\Core\Request */
        $request = $this->container->get('request');
        echo "Method: " . $request->getMethod();
    }
    
    private function getCoreComponents(): array {
        return [
            'request' => function() {
                return new Core\Request();
            }
        ];
    }
    
    
}