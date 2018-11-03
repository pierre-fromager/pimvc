<?php

/**
 * Description of Pimvc\Db\Model\Domain
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\Db\Model;

abstract class Domain implements \Pimvc\Db\Model\Interfaces\Domain
{
    public $counter;


    public function __construct()
    {
    }

    /**
     * Check if a string is serialized
     *
     * @param string $string
     */
    protected static function isSerialised($string)
    {
        return (@unserialize($string) !== false);
    }

    /**
     * getCounter
     *
     * @return int
     */
    public function getCounter()
    {
        return $this->counter;
    }

    /**
     * countParts
     *
     * @return int
     */
    public function countParts($partSize = 0)
    {
        $partSize = ($partSize) ? $partSize : self::MAXPARTS;
        $nbPart = floor(count($this->getVars()) / $partSize);
        return $nbPart;
    }

    /**
     * getPart
     *
     * @param int $part
     * @return array
     */
    public function getPart($part)
    {
        return array_slice($this->getVars(), $part * self::MAXPARTS, self::MAXPARTS);
    }

    /**
     * hydrate assigns values
     *
     * @param type $array
     */
    public function hydrate($array)
    {
        $classKeys = array_keys(get_class_vars(get_called_class()));
        foreach ($classKeys as $property) {
            $value = (isset($array[$property]))
                ? $array[$property]
                : self::FORBIDENKEYS;
            if (property_exists($this, $property)
                    && ($value !== self::FORBIDENKEYS)
            ) {
                $this->$property = (self::isSerialised($value))
                    ? unserialize($value)
                    : $value;
            } else {
                unset($this->$property);
            }
        }
    }

    /**
     * get
     *
     * @return \Pimvc\Db\Model\Domain
     */
    public function get()
    {
        return $this;
    }

    /**
     * getProperties
     *
     * @return array
     */
    public function getProperties()
    {
        $propertiesList = array_keys(get_class_vars(get_called_class()));
        if (($key = array_search('counter', $propertiesList)) !== false) {
            unset($propertiesList[$key]);
        }
        $propList = [];
        $className = get_called_class();
        $cacheManager = Lib_Db_Model_Domain_Cache_Manager::getInstance();
        if ($cacheManager->has($className) && !self::DEBUG_MODE) {
            $propList = $cacheManager->get($className);
        } else {
            $propertiesCache = new Cache(get_called_class()); // 5mn expiration
            $propertiesCachePath = APP_PATH . '/cache/Db/';
            $propertiesCache->setPath($propertiesCachePath);
            if ($propertiesCache->expired()) {
                foreach ($propertiesList as $propertyName) {
                    $propList[$propertyName] = $this->getProperty($propertyName);
                }
                $propertiesCache->set($propList);
            } else {
                $propList = $propertiesCache->get();
            }
            $cacheManager->set($className, $propList);
        }
        return $propList;
    }

    /**
     * getPk
     *
     * @return array
     */
    public function getPks()
    {
        $props = $this->getProperties();
        $pks = [];
        foreach ($props as $prop) {
            $name = (isset($prop[self::KEY_NAME]))
                ? $prop[self::KEY_NAME]
                : $prop[self::KEY_VAR];
            $pk = $this->getPk($name);
            if ($pk && $pk != 'null') {
                $pks[] = $name;
            }
        }
        return $pks;
    }

    /**
     * getPk
     *
     * @return array
     */
    public function getPk($propertyName)
    {
        $prop = $this->getProperty($propertyName);
        return (isset($prop[self::KEY_PK])) ? $prop[self::KEY_PK] : false;
    }


    /**
     * getBooleans
     *
     * @return array
     */
    public function getBooleans()
    {
        $pdos = $this->getPdos();
        $booleans = [];
        foreach ($pdos as $pdoName => $pdoValue) {
            if ($pdoValue == 5) {
                $booleans[] = $pdoName;
            }
        }
        return $booleans;
    }

    /**
     * getIndex
     *
     * @return array
     */
    public function getIndex($propertyName)
    {
        $prop = $this->getProperty($propertyName);
        return (isset($prop[self::KEY_INDEX])) ? $prop[self::KEY_INDEX] : false;
    }


    /**
     * getIndexes
     *
     * @return array
     */
    public function getIndexes()
    {
        $props = $this->getProperties();
        $indexes = [];
        foreach ($props as $prop) {
            $name = (isset($prop[self::KEY_NAME]))
                ? $prop[self::KEY_NAME]
                : $prop[self::KEY_VAR];
            $index = $this->getIndex($name);
            if ($index && ($index == 1)) {
                $indexes[] = $name;
            }
        }
        return array_slice($indexes, 0, self::maxColumns);
    }

    /**
     * getAllIndexes
     *
     * @return array
     */
    public function getAllIndexes()
    {
        $props = $this->getProperties();
        $indexes = [];
        foreach ($props as $prop) {
            $name = (isset($prop[self::KEY_NAME]))
                ? $prop[self::KEY_NAME]
                : $prop[self::KEY_VAR];
            $index = $this->getIndex($name);
            if ($index && ($index == 1)) {
                $indexes[] = $name;
            }
        }
        return $indexes;
    }

    /**
     * getLengths
     *
     * @return array
     */
    public function getLengths()
    {
        $props = $this->getProperties();
        $lengths = [];
        foreach ($props as $prop) {
            $name = (isset($prop[self::KEY_NAME]))
                ? $prop[self::KEY_NAME]
                : $prop[self::KEY_VAR];
            $length = $this->getLength($name);
            if ($length) {
                $lengths[$name] = $length;
            }
        }
        return $lengths;
    }

    /**
     * getLength
     *
     * @return array
     */
    public function getLength($propertyName)
    {
        $prop = $this->getProperty($propertyName);
        return ($this->hasLength($propertyName)) ? $prop[self::KEY_LENGTH] : false;
    }

    /**
     * hasLength
     *
     * @return array
     */
    public function hasLength($propertyName)
    {
        $prop = $this->getProperty($propertyName);
        return (isset($prop[self::KEY_LENGTH]));
    }

    /**
     * getPdo
     *
     * @return array
     */
    public function getPdo($propertyName)
    {
        $prop = $this->getProperty($propertyName);
        return ($this->hasPdo($propertyName)) ? $prop[self::KEY_PDO] : false;
    }

    /**
     * hasPdo
     *
     * @return array
     */
    public function hasPdo($propertyName)
    {
        $prop = $this->getProperty($propertyName);
        return (isset($prop[self::KEY_PDO]));
    }

    /**
     * getPdos
     *
     * @return array
     */
    public function getPdos()
    {
        $props = $this->getProperties();
        $pdos = [];
        foreach ($props as $prop) {
            $name = (isset($prop[self::KEY_NAME]))
                ? $prop[self::KEY_NAME]
                : $prop[self::KEY_VAR];
            $pdos[$name] = $this->getPdo($name);
        }
        return $pdos;
    }

    /**
     * getVars
     *
     * @return array
     */
    public function getVars()
    {
        $vars = array_keys(get_class_vars(get_called_class()));
        if (($key = array_search('counter', $vars)) !== false) {
            unset($vars[$key]);
        }
        return $vars;
    }

    /**
     * getVars
     *
     * @return array
     */
    public function getVarsByKeyword($keyword)
    {
        $vars = array_keys(get_class_vars(get_called_class()));

        if (($key = array_search('counter', $vars)) !== false) {
            unset($vars[$key]);
        }
        $filtered_vars = array_filter(
            $vars,
            function ($element) use ($keyword) {
                return (strpos($element, $keyword) !== false);
            }
        );
        return $filtered_vars;
    }

    /**
     * getProperty
     *
     * @param string $propertyName
     * @return array
     */
    public function getProperty($propertyName)
    {
        $o = new \ReflectionObject($this);
        $props = [];
        try {
            $p = $o->getProperty($propertyName);
            $dc = $p->getDocComment();
            $dc = str_replace(array('*', '/'), '', $dc);
            $dces = explode('@', $dc);
            unset($dces[0]);
            foreach ($dces as $dce) {
                $propsArray = explode(' ', $dce);
                $k = $propsArray[0];
                $v = trim($propsArray[1]);
                $props[$k] = (is_numeric($v)) ? (int) $v : (string) $v;
            }
        } catch (ReflectionException $e) {
            if (self::DEBUG_MODE) {
                echo '<p style="color:red">Error '
                . __METHOD__ . ' - ' . $e->getMessage()
                . '</p>' . '<pre>' . $e->getTraceAsString() . '</pre>' . '<hr>';
            }
        }
        unset($o);
        return $props;
    }

    /**
     * getModelName
     *
     * @return string
     */
    public function getModelName()
    {
        return str_replace('_Domain', '', get_class($this));
    }

    /**
     * getModelRefMap
     *
     * @return array
     */
    public function getModelRefMap()
    {
        $modelName = $this->getModelName();
        $modelInstance = new $modelName;
        $refMap = $modelInstance->getRefMap();
        unset($modelName);
        unset($modelInstance);
        return $refMap;
    }

    /**
     * getWeight
     *
     * @return int
     */
    public function getWeight()
    {
        return sizeof($this, COUNT_RECURSIVE);
    }

    /**
     * getMaxWeight
     *
     * @return int
     */
    public function getMaxWeight()
    {
        $maxWeight = 0;
        $propterties = $this->getProperties();
        foreach ($propterties as $key => $value) {
            $maxWeight += $value[self::KEY_LENGTH];
        }
        return $maxWeight;
    }
}
