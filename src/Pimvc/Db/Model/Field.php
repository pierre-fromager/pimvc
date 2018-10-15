<?php

/**
 * Description of Pimvc\Db\Model\Field
 *
 * Used to analyse datas source([][]||[]) and set properties belongs
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Db\Model;

class Field
{

    const _STRLEN = 'strlen';
    const _IS_NUMERIC = 'is_numeric';
    const _IS_NULL = 'is_null';

    /**
     * $count
     * @var int
     */
    protected $count;

    /**
     * $name
     * @var string
     */
    protected $name;

    /**
     * $maxLen
     * @var int
     */
    protected $maxLen;

    /**
     * $isFloat
     * @var bool
     */
    protected $isFloat;

    /**
     * $isInt
     * @var boolean
     */
    protected $isInt;

    /**
     * $isString
     * @var bool
     */
    protected $isString;

    /**
     * $isNumeric
     * @var bool
     */
    protected $isNumeric;

    /**
     * $isNullable
     * @var bool
     */
    protected $isNullable;

    /**
     * $isUniq
     * @var bool
     */
    protected $isUniq;

    /**
     * $stack
     * @var array
     */
    private $stack;

    /**
     * __construct
     *
     */
    public function __construct()
    {
        $this->init();
        return $this;
    }

    /**
     * init
     */
    protected function init()
    {
        $this->count = 0;
        $this->name = '';
        $this->isFloat = false;
        $this->isInt = false;
        $this->isString = false;
        $this->isNumeric = false;
        $this->isNullable = false;
        $this->isUniq = false;
    }

    /**
     * getCount
     *
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * getMaxLength
     *
     * @return int
     */
    public function getMaxLen(): int
    {
        return $this->maxLen;
    }

    /**
     * getName
     *
     * @return bool
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * getIsString
     *
     * @return bool
     */
    public function getIsString(): string
    {
        return $this->isString;
    }

    /**
     * getIsFloat
     *
     * @return boolean
     */
    public function getIsFloat(): bool
    {
        return $this->isFloat;
    }

    /**
     * getIsInt
     *
     * @return bool
     */
    public function getIsInt(): bool
    {
        return $this->isInt;
    }

    /**
     * getIsNumeric
     *
     * @return bool
     */
    public function getIsNumeric(): bool
    {
        return $this->isNumeric;
    }

    /**
     * getIsNullable
     *
     * @return bool
     */
    public function getIsNullable(): bool
    {
        return $this->isNullable;
    }

    /**
     * getIsUniq
     *
     * @return bool
     */
    public function getIsUniq(): bool
    {
        return $this->isUniq;
    }

    /**
     * getAsArray
     *
     * @return array
     */
    public function getAsArray(): array
    {
        return [
            'count' => $this->count,
            'name' => $this->name,
            'isUniq' => $this->isUniq,
            'isNullable' => $this->isNullable,
            'isNumeric' => $this->isNumeric,
            'isFloat' => $this->isFloat,
            'isInt' => $this->isInt,
            'isString' => $this->isString,
            'maxLen' => $this->maxLen,
        ];
    }

    /**
     * setName
     *
     * @param string $name
     * @return \Pimvc\Db\Model\Field
     */
    public function setName(string $name): Field
    {
        $this->name = $name;
        return $this;
    }

    /**
     * setCount
     *
     * @param int $count
     * @return \Pimvc\Db\Model\Field
     */
    public function setCount(int $count): Field
    {
        $this->count = $count;
        return $this;
    }

    /**
     * setIsNumeric
     *
     * @param bool $isNumeric
     * @return \Pimvc\Db\Model\Field
     */
    public function setIsNumeric(bool $isNumeric): Field
    {
        $this->isNumeric = $isNumeric;
        return $this;
    }

    /**
     * setMaxlen
     *
     * @param int $maxLen
     * @return \Pimvc\Db\Model\Field
     */
    public function setMaxlen(int $maxLen): Field
    {
        $this->maxLen = $maxLen;
        return $this;
    }

    /**
     * setIsString
     *
     * @param bool $isString
     * @return \Pimvc\Db\Model\Field
     */
    public function setIsString(bool $isString): Field
    {
        $this->isString = $isString;
        return $this;
    }

    /**
     * setIsFloat
     *
     * @param bool $isFloat
     * @return \Pimvc\Db\Model\Field
     */
    public function setIsFloat(bool $isFloat): Field
    {
        $this->isFloat = $isFloat;
        return $this;
    }

    /**
     * setIsInt
     *
     * @param bool $isInt
     * @return \Pimvc\Db\Model\Field
     */
    public function setIsInt(bool $isInt): Field
    {
        $this->isInt = $isInt;
        return $this;
    }

    /**
     * setIsUniq
     *
     * @param bool $isUniq
     * @return \Pimvc\Db\Model\Field
     */
    public function setIsUniq(bool $isUniq): Field
    {
        $this->isUniq = $isUniq;
        return $this;
    }

    /**
     * setIsNullable
     *
     * @param bool $isNullable
     * @return \Pimvc\Db\Model\Field
     */
    public function setIsNullable(bool $isNullable): Field
    {
        $this->isNullable = $isNullable;
        return $this;
    }

    /**
     * setFromData
     *
     * @param array $dataGrid
     * @param string $fieldName
     * @return $this
     */
    public function setFromData(array $dataGrid, string $fieldName = ''): Field
    {
        $this->name = ($fieldName) ? $fieldName : $this->name;
        $this->setStackFromDatas($dataGrid, $fieldName);
        $this->maxLen = $this->computedMaxLength();
        $this->isNumeric = $this->computedIsNumeric();
        $this->isString = $this->computedIsString();
        $this->isUniq = $this->computedIsUniq();
        $this->isNullable = $this->computedIsNullable();
        if ($this->isNumeric) {
            $this->isFloat = $this->computedIsFloat();
            $this->isInt = $this->computedIsInt();
        }
        $this->stack = [];
        return $this;
    }

    /**
     * setStackFromDatas
     *
     * @param array $datas
     * @param string $fieldName
     */
    private function setStackFromDatas(array $datas, string $fieldName = ''): void
    {
        $this->count = count($datas);
        $this->stack = [];
        for ($c = 0; $c < $this->count; ++$c) {
            $this->stack[] = ($fieldName) ? $datas[$c][$fieldName] : $datas[$c];
        }
    }

    /**
     * computedMaxLength
     *
     * @return int
     */
    private function computedMaxLength(): int
    {
        return max(array_map(self::_STRLEN, $this->stack));
    }

    /**
     * computedIsNumeric
     *
     * @return boolean
     */
    private function computedIsNumeric(): bool
    {
        return (count(array_filter($this->stack, self::_IS_NUMERIC)) === $this->count);
    }

    /**
     * computedIsUniq
     *
     * @return boolean
     */
    private function computedIsUniq(): bool
    {
        return (count(array_unique($this->stack)) === $this->count);
    }

    /**
     * computedIsUniq
     *
     * @return boolean
     */
    private function computedIsNullable(): bool
    {
        return (count(array_filter($this->stack, self::_IS_NULL)) === $this->count);
    }

    /**
     * computedIsString
     *
     * @return bool
     */
    private function computedIsString(): bool
    {
        return !$this->isNumeric;
    }

    /**
     * computedIsFloat
     *
     * @return bool
     */
    private function computedIsFloat(): bool
    {
        return count(
            array_filter($this->stack, function ($v) {
                    return strpos($v, '.') != false;
            })
        ) == $this->count;
    }

    /**
     * computedIsInt
     *
     * @return bool
     */
    private function computedIsInt(): bool
    {
        return ($this->isNumeric && !$this->isFloat && !$this->isString);
    }
}
