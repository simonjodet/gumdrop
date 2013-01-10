<?php
/**
 * Generic configuration container
 * @package Gumdrop
 */
namespace Gumdrop;

class Configuration implements \Iterator, \Countable, \ArrayAccess
{
    protected $configuration = array();

    private $position = 0;


    public function __construct($configuration = array())
    {
        $this->configuration = $configuration;
    }

    public function extract()
    {
        return $this->configuration;
    }

    public function current()
    {
        return $this->configuration[$this->position];
    }

    public function next()
    {
        $this->position++;
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
        return isset($this->configuration[$this->position]);
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function offsetExists($offset)
    {
        return isset($this->configuration[$offset]);
    }

    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            return null;
        }
        return $this->configuration[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $offset = count($this->configuration);
        }
        $this->configuration[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->configuration[$offset]);
    }

    public function count()
    {
        return count($this->configuration);
    }

}
