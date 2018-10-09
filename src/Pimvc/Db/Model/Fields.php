<?php
namespace Pimvc\Db\Model;

class Fields implements \ArrayAccess, \Countable
{

    const _BAD_INSTANCE = 'value must be an instance of Field';

    private $container = [];

    /**
     * offsetSet
     *
     * @param type $offset
     * @param \Pimvc\Db\Model\Field $value
     * @throws Exception
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof \Pimvc\Db\Model\Field) {
            throw new Exception(self::_BAD_INSTANCE);
        }

        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * offsetExists
     *
     * @param type $offset
     * @return type
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * offsetUnset
     *
     * @param type $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * offsetGet
     *
     * @param type $offset
     * @return type
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * addItem
     *
     * @param \Pimvc\Db\Model\Field $field
     */
    public function addItem(\Pimvc\Db\Model\Field $field): void
    {
        $this->container[] = $field;
    }

    /**
     * count
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->container);
    }
}
