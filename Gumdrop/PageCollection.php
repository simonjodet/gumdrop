<?php
/**
 * Page collection
 * @package Gumdrop
 */

namespace Gumdrop;

class PageCollection implements \Iterator, \Countable, \ArrayAccess
{
    private $Pages = array();

    private $position = 0;

    public function __construct($Pages = array())
    {
        if (count($Pages) > 0)
        {
            foreach ($Pages as $Page)
            {
                $this->offsetSet(null, $Page);
            }
        }
    }

    public function exportForTwigRendering()
    {
        $extract = array();
        foreach ($this->Pages as $Page)
        {
            /**
             * @var $Page \Gumdrop\Page
             */
            $extract[] = $Page->exportForTwigRendering();
        }
        return $extract;
    }

    public function current()
    {
        return $this->Pages[$this->position];
    }

    public function next()
    {
        ++$this->position;
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
        return isset($this->Pages[$this->position]);
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function offsetExists($offset)
    {
        return isset($this->Pages[$offset]);
    }

    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset))
        {
            return null;
        }
        return $this->Pages[$offset];
    }

    public function offsetSet($offset, $Page)
    {
        if (!$Page instanceof \Gumdrop\Page)
        {
            throw new Exception('Expecting an instance of \Gumdrop\Page');
        }

        if (is_null($offset))
        {
            $offset = count($this->Pages);
        }

        $this->Pages[$offset] = $Page;
    }

    public function offsetUnset($offset)
    {
        unset($this->Pages[$offset]);
    }

    public function count()
    {
        return count($this->Pages);
    }
}

