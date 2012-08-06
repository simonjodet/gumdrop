<?php
/**
 * Page-level configuration container
 * @package Gumdrop
 */

namespace Gumdrop;

/**
 * Page-level configuration container
 */
class PageCollection implements \Iterator, \Countable, \ArrayAccess
{
    /**
     * Array of the Pages
     * @var array
     */
    private $Pages = array();

    /**
     * Current offset
     * @var int
     */
    private $position = 0;

    /**
     * Constructor
     *
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
     * Appends a Page to the collection
     *
     * @param \Gumdrop\Page $Page
     */
    public function add(\Gumdrop\Page $Page)
    {
        $this->offsetSet(null, $Page);
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->Pages[$this->position];
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean
     * The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return isset($this->Pages[$this->position]);
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset An offset to check for.
     *
     * @return boolean true on success or false on failure.
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return isset($this->Pages[$offset]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset
     * The offset to retrieve.
     *
     * @return mixed Can return all value types.
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
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset
     * The offset to assign the value to.
     *
     * @param \Gumdrop\Page $Page
     * The Page to set.
     *
     * @return void
     *
     * @throws \Gumdrop\Exception Message: Expecting an instance of \Gumdrop\Page
     */
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

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset
     * The offset to unset.
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->Pages[$offset]);
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->Pages);
    }
}

