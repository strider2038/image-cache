<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Collection;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
abstract class AbstractClassCollection extends ArrayCollection
{
    /** @var string */
    private $className;

    /**
     * @param array $elements
     * @param string $className
     * @throws \DomainException
     */
    public function __construct(array $elements = [])
    {
        $this->className = $this->getElementClassName();

        if (!class_exists($this->className) && !interface_exists($this->className)) {
            throw new \DomainException(
                sprintf('Collection element class "%s" does not exist', $this->className)
            );
        }

        foreach ($elements as $element) {
            $this->validateElement($element);
        }

        parent::__construct($elements);
    }

    abstract protected function getElementClassName(): string;

    /**
     * @inheritdoc
     * @throws \DomainException
     */
    public function set($key, $value): void
    {
        $this->validateElement($value);
        parent::set($key, $value);
    }

    /**
     * @inheritdoc
     * @throws \DomainException
     */
    public function add($element): bool
    {
        $this->validateElement($element);
        return parent::add($element);
    }

    /**
     * @param AbstractClassCollection $collection
     * @throws \DomainException
     */
    public function merge(AbstractClassCollection $collection): void
    {
        foreach ($collection as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @param AbstractClassCollection $collection
     * @throws \DomainException
     */
    public function append(AbstractClassCollection $collection): void
    {
        foreach ($collection as $element) {
            $this->add($element);
        }
    }

    /**
     * @param $value
     * @throws \DomainException
     */
    private function validateElement($value): void
    {
        if (!$value instanceof $this->className) {
            throw new \DomainException(sprintf('Collection element must be instance of %s', $this->className));
        }
    }
}
