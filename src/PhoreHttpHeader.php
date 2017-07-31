<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 7/31/17
 * Time: 1:27 AM
 */

namespace Phore\Http;


class PhoreHttpHeader implements \ArrayAccess, \Iterator
{


    private $headers = [];
    private $hashIndex = [];
    private $index = [];


    public function __construct()
    {
    }


    public function set(string $name, string $value) : self {
        $this[$name] = $value;
        return $this;
    }


    /**
     * Whether a offset exists
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset ($this->hashIndex[strtolower($offset)]);// TODO: Implement offsetExists() method.
    }

    /**
     * Offset to retrieve
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     *
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        if ( ! isset ($this->hashIndex[strtolower($offset)]))
            return null;
        return $this->headers[$this->hashIndex[strtolower($offset)]];
    }

    /**
     * Offset to set
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        if ( ! isset ($this->hashIndex[strtolower($offset)]))
            $this->index[] = $offset;
        $this->headers[$offset] = $value;
        $this->hashIndex[strtolower($offset)] = $offset;
    }

    /**
     * Offset to unset
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset) {
        $newIndex = [];
        foreach ($this->index as $curVal) {
            if ($curVal === strtolower($offset))
                continue;
            $newIndex[] = $curVal;
        }
        $this->index = $newIndex;
        unset ($this->headers[$this->hashIndex[strtolower($offset)]]);
    }

    private $iteratorIndex = 0;

    /**
     * Return the current element
     *
     * @link  http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return $this->headers[$this->index[$this->iteratorIndex]];
    }

    /**
     * Move forward to next element
     *
     * @link  http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $this->iteratorIndex++;
    }

    /**
     * Return the key of the current element
     *
     * @link  http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->index[$this->iteratorIndex];
    }

    /**
     * Checks if current position is valid
     *
     * @link  http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return isset ($this->index[$this->iteratorIndex]);
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @link  http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->iteratorIndex = 0;
    }
}