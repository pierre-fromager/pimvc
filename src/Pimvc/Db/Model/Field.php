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
    const _PRIMARY = 'primary';
    const _KEY = 'key';

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
     * $decimalSeparator
     * @var string
     */
    protected $decimalSeparator;

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
     * $isKey
     * @var bool
     */
    protected $isKey;

    /**
     * $isPrimaryKey
     * @var bool
     */
    protected $isPrimaryKey;

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
     * toArray
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'maxLen' => $this->maxLen,
            'isFloat' => $this->isFloat,
            'isInt' => $this->isInt,
            'isString' => $this->isString,
            'isNumeric' => $this->isNumeric,
            'isNullable' => $this->isNullable,
            'isUniq' => $this->isUniq,
            'isKey' => $this->isKey,
            'isPrimaryKey' => $this->isPrimaryKey,
            'count' => $this->count,
        ];
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * getDecimalSeparator
     *
     * @return string
     */
    public function getDecimalSeparator(): string
    {
        return $this->decimalSeparator;
    }

    /**
     * getIsString
     *
     * @return bool
     */
    public function getIsString(): bool
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
     * getIsKey
     *
     * @return bool
     */
    public function getIsKey(): bool
    {
        return $this->isKey;
    }

    /**
     * getIsPrimaryKey
     *
     * @return bool
     */
    public function getIsPrimaryKey(): bool
    {
        return $this->isPrimaryKey;
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
            'decimalSeparator' => $this->decimalSeparator
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
     * setDecimalSeparator
     *
     * @param string $separator
     * @return \Pimvc\Db\Model\Field
     */
    public function setDecimalSeparator(string $separator): Field
    {
        $this->decimalSeparator = $separator;
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
     * setIsKey
     *
     * @param bool $isKey
     * @return \Pimvc\Db\Model\Field
     */
    public function setIsKey(bool $isKey): Field
    {
        $this->isKey = $isKey;
        return $this;
    }

    /**
     * setIsPrimaryKey
     *
     * @param bool $isPrimaryKey
     * @return \Pimvc\Db\Model\Field
     */
    public function setIsPrimaryKey(bool $isPrimaryKey): Field
    {
        $this->isPrimaryKey = $isPrimaryKey;
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
     * @param array $dataGrid
     * @param string $fieldName
     * @return \Pimvc\Db\Model\Field
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
     * setFromDescribe
     * @param string $adapter
     * @param array $desc
     * @return \Pimvc\Db\Model\Field
     * @throws Exception
     */
    public function setFromDescribe(string $adapter, array $desc): Field
    {
        $allowedAdapter = [
            \Pimvc\Db\Model\Core::MODEL_ADAPTER_MYSQL,
            \Pimvc\Db\Model\Core::MODEL_ADAPTER_SQLITE,
            \Pimvc\Db\Model\Core::MODEL_ADAPTER_PGSQL
        ];
        if (!in_array($adapter, $allowedAdapter)) {
            throw new \Exception('Unmanaged adapter', 1);
        }
        $this->setIsPrimaryKey($desc[self::_PRIMARY]);
        $this->setIsKey($desc[self::_KEY]);
        switch ($adapter) {
            case \Pimvc\Db\Model\Core::MODEL_ADAPTER_MYSQL:
                $this->setName($desc['field']);
                $this->setIsNullable($desc['null'] === 'YES');
                $type = $desc['type'];
                preg_match('#\((.*?)\)#', $type, $lenCapture);
                $len = (isset($lenCapture[1])) ? (int) $lenCapture[1] : 0;
                $this->setMaxlen($len);
                $isString = (preg_match('/^varchar/', $type) === 1);
                $this->setIsString($isString);
                $this->setIsNumeric(!$isString);
                if (!$isString) {
                    $this->setIsInt((preg_match('/^int/', $type) === 1));
                    $this->setIsFloat((preg_match('/^float/', $type) === 1));
                }
                break;

            case \Pimvc\Db\Model\Core::MODEL_ADAPTER_SQLITE:
                $this->setName($desc['name']);
                $this->setIsNullable($desc['notnull'] === '1');
                $type = $desc['type'];
                $isString = (preg_match('/^(TEXT|DATETIME)/', $type) === 1);
                $this->setIsString($isString);
                $this->setIsNumeric(!$isString);
                if (!$isString) {
                    $this->setIsInt((preg_match('/^INTEGER/', $type) === 1));
                    $this->setIsFloat((preg_match('/^REAL/', $type) === 1));
                }
                break;

            case \Pimvc\Db\Model\Core::MODEL_ADAPTER_PGSQL:
                $this->setName($desc['column_name']);
                $this->setIsNullable($desc['is_nullable'] === 'YES');
                $type = $desc['data_type'];
                $isString = (preg_match('/(^character|^text)/', $type) === 1);
                $this->setMaxlen((int) $desc['character_maximum_length']);
                $this->setIsString($isString);
                $this->setIsNumeric(!$isString);
                if (!$isString) {
                    $this->setIsInt((preg_match('/^integer/', $type) === 1));
                    $this->setIsFloat((preg_match('/^real/', $type) === 1));
                }
                break;
        }
        return $this;
    }

    /**
     * setStackFromDatas
     *
     * @param array $datas
     * @param string $fieldName
     */
    private function setStackFromDatas(array $datas, string $fieldName = '')
    {
        $this->count = count($datas);
        $this->stack = [];
        for ($c = 0; $c < $this->count; ++$c) {
            if (isset($datas[$c][$fieldName])) {
                $this->stack[] = $datas[$c][$fieldName];
            }
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
        //$isNumeric = array_filter($this->stack, self::_IS_NUMERIC);
        //echo '<pre>' . print_r([$this->name, $isNumeric, $this->count], true) . '</pre>';
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
