<?php

/**
 * Description of domain
 *
 * @author pierrefromager
 */

namespace pimvc\db\model\interfaces;

interface domain {

    const forbidenKeys = '@Bye';
    const maxColumns = 54;
    const maxParts = 20;
    const DEBUG_MODE = false;
    const KEY_NAME = 'name';
    const KEY_VAR = 'var';
    const KEY_LENGTH = 'length';
    const KEY_PDO = 'pdo';
    const KEY_PK = 'pk';
    const KEY_INDEX = 'index';

    public function getCounter();

    public function countParts($partSize);

    public function getPart($part);

    public function hydrate($array);

    public function get();

    public function getProperties();

    public function getPks();

    public function getPk($propertyName);

    public function getBooleans();

    public function getIndex($propertyName);

    public function getIndexes();

    public function getAllIndexes();

    public function getLengths();

    public function getLength($propertyName);

    public function hasLength($propertyName);

    public function getPdo($propertyName);

    public function hasPdo($propertyName);

    public function getPdos();

    public function getVars();

    public function getVarsByKeyword($keyword);

    public function getProperty($propertyName);

    public function getModelName();

    public function getModelRefMap();

    public function getWeight();

    public function getMaxWeight();
}
