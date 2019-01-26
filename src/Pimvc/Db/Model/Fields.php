<?php
namespace Pimvc\Db\Model;

class Fields implements \ArrayAccess, \Countable, \IteratorAggregate
{

    const _BAD_INSTANCE = 'value must be an instance of Field';

    /**
     * $container
     * @var \Pimvc\Db\Model\Field[]
     */
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
    public function addItem(\Pimvc\Db\Model\Field $field)
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

    /**
     * getIterator
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->container);
    }

    /**
     * toArray
     *
     * @return array
     */
    public function toArray(): array
    {
        $datas = [];
        for ($c = 0; $c < count($this->container); $c++) {
            $datas[] = $this->container[$c]->toArray();
        }
        return $datas;
    }

    /**
     * getIndexes
     *
     * @return array
     */
    public function getIndexes($asName = false): array
    {
        $datas = [];
        for ($c = 0; $c < count($this->container); $c++) {
            if ($this->container[$c]->getIsKey()) {
                $datas[] = ($asName) ? $this->container[$c]->getName() : $this->container[$c]->toArray();
            }
        }
        return $datas;
    }

    /**
     * getPdos
     *
     * @return array
     */
    public function getPdos(): array
    {
        $datas = [];
        for ($c = 0; $c < count($this->container); $c++) {
            $name = $this->container[$c]->getName();
            $datas[$name] = $this->container[$c]->getPdoType();
        }
        return $datas;
    }
}
