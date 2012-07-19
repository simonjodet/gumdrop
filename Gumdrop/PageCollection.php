<?php

namespace Gumdrop;

require_once __DIR__ . '/Page.php';

/**
 * \Gumdrop\Page collection
 */
class PageCollection implements \Iterator, \Countable, \ArrayAccess
{
    /**
     * @var array
     */
    private $Pages = array();
    /**
     * @var int
     */
    private $position = 0;

    /**
     * @param array $Pages
     */
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

    /**
     * @param \Gumdrop\Page $Page
     */
    public function add(\Gumdrop\Page $Page)
    {
        $this->offsetSet(null, $Page);
    }

    /**
     * @return \Gumdrop\Page
     */
    public function current()
    {
        return $this->Pages[$this->position];
    }

    /**
     * @return null
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return isset($this->Pages[$this->position]);
    }

    /**
     * @return null
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * @param int $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->Pages[$offset]);
    }

    /**
     * @param int $offset
     *
     * @return \Gumdrop\Page
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset))
        {
            return null;
        }
        return $this->Pages[$offset];
    }

    /**
     * @param int|null      $offset
     * @param \Gumdrop\Page $Page
     *
     * @throws \Exception
     */
    public function offsetSet($offset, $Page)
    {
        if (!$Page instanceof \Gumdrop\Page)
        {
            throw new \Exception('Expecting an instance of \Gumdrop\Page');
        }

        if (is_null($offset))
        {
            $offset = count($this->Pages);
        }

        $this->Pages[$offset] = $Page;
    }

    /**
     * @param int $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->Pages[$offset]);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->Pages);
    }
}

