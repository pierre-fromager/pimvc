<?php

/**
 * Description of domain
 *
 * @author pierrefromager
 */

namespace lib\db\model\interfaces;

interface orm {
        
    const MODEL_DEBUG = true;
    const MODEL_TRACE = true;
    //const MODEL_FETCH_MODE = PDO::FETCH_ASSOC;
    const MODEL_TRANS = ':TRANS_';
    const MODEL_FROM = ' FROM ';
    const MODEL_SELECT = 'SELECT ';
    const MODEL_SELECT_COUNT = 'SELECT COUNT';
    const MODEL_JOIN = ' JOIN ';
    const MODEL_JOIN_INNER = ' INNER ';
    const MODEL_JOIN_OUTER = ' OUTER ';
    const MODEL_JOIN_LEFT = ' LEFT ';
    const MODEL_JOIN_RIGHT = ' RIGHT ';
    const MODEL_JOIN_NATURAL = ' NATURAL ';
    const MODEL_JOIN_FULL = ' FULL ';
    const MODEL_JOIN_UNION = ' UNION ';
    const MODEL_JOIN_CROSS = ' CROSS ';
    const MODEL_JOIN_ON = ' ON ';
    const MODEL_TRUNCATE = 'TRUNCATE TABLE ';
    const MODEL_DOT = '.';
    const MODEL_WHERE = ' WHERE ';
    const MODEL_ORDER = ' ORDER BY ';
    const MODEL_ORDER_ASC = 'ASC';
    const MODEL_ORDER_DESC = 'DESC';
    const MODEL_UPDATE = ' UPDATE ';
    const MODEL_SET = ' SET ';
    const MODEL_INSERT = ' INSERT INTO ';
    const MODEL_DELETE = ' DELETE FROM ';
    const MODEL_ALIAS = ' AS ';
    const MODEL_LIKE = ' LIKE ';
    const MODEL_EQUAL = ' = ';
    const MODEL_AND = ' AND ';
    const MODEL_OR = ' OR ';
    const MODEL_ALTER = ' ALTER TABLE ';
    const MODEL_ADD = ' ADD ';
    const MODEL_DROP = ' DROP ';
    const MODEL_LIMIT = ' LIMIT ';
    const MODEL_OFFSET = ' OFFSET ';
    const MODEL_GROUP_BY = ' GROUP BY ';
    const MODEL_OPERATOR_SPLITER = '#';
    const MODEL_DOMAIN_CLASS_PREFIX = '\model\domain\\';
    const MODEL_ADAPTER_DEFAULT = 'pdomysql';
    const MODEL_ADAPTER_MYSQL = self::MODEL_ADAPTER_DEFAULT;
    const MODEL_ADAPTER_PGSQL = 'pdopgsql';
    const MODEL_ADAPTER_SQLITE = 'pdosqlite';
    const MODEL_ADAPTER_4D = 'pdo4d';
    const MODEL_PARENTH_O = '(';
    const MODEL_PARENTH_C = ')';
    const MODEL_CACHE_SUFFIX = 'Db/Queries/';
    const MODEL_USE_CACHE = true;
    const MODEL_INDEX_FIELD = 'field';
    const MODEL_OPERATOR_TRIGGER = '[]%_';
    const MODEL_BETWEEN = ' BETWEEN ';
    const MODEL_DOMAIN = 'domain';
    const BACKSLASH = '\\';

    public function __construct($config = array());

    public function setCast($fieldName, $typeCast);

    public function setCasts($fieldsCast);

    public function isPgsql();

    public function getName();

    public function getDefaultSchema();

    public function getAdapter();

    public function getStatement();

    public function getDomainInstance();

    public function getColumns();

    public function setFetchMode($mode);

    public function getFetchMode();

    public function getSql();

    public function getSize();

    public function getPrimary();

    public function describeTable($name = '');

    public function showTable();

    public function getError();

    public function hasError();

    public function getErrorCode();

    public function getErrorMessage();

    public function cleanRowset();

    public function getRowset();

    public function getRowsetAsArray($preservedKey = '', $assignedKeyValue = '');

    public function getCurrent();

    public function previous();

    public function next();

    public function rewind();

    public function seek($value = 0);

    public function save($domainObject, $forceAsNew = false);

    public function saveDiff($domainObject, $forceAsNew = false);

    public function getDiffDomainObject(\lib\db\model\domain $d1, \lib\db\model\domain $d2);

    public function setOr($params);

    public function setParenthesis($params);

    public function setPatchWere($patch);

    public function _getOrder($orders);

    public function _getLimit($limits);

    public function getParts($ri, $where);

    public function getDependantObjects($key, $value, $deepness = 0);

    public function getAlias();

    public function join($ft, $fc, $lt, $lc);

    public function innerJoin($ft, $fc, $lt, $lc);

    public function outerJoin($ft, $fc, $lt, $lc);

    public function naturalJoin($ft, $fc, $lt, $lc);

    public function leftJoin($ft, $fc, $lt, $lc);

    public function rightJoin($ft, $fc, $lt, $lc);

    public function find();

    public function counter($where);

    public function add($mapperInstance);

    public function update($params = array());

    public function directsql($sql, $params);

    public function delete();

    public function multidelete();

    public function insert($params = array(), $forgetPrimary = true);

    public function getSum($column, $criterias = array(), $precision = 2);

    public function getAvg($column, $criterias = array(), $precision = 2);

    public function getMathFn($fn, $column, $criterias = array(), $precision = 2, $precisionFn = 'round');

    public function run($sql, $bindParams = array(), $bindTypes = array());

    public function getLastInsertId();

    public function setWhere($criterias);

    public function getWhere($asCriterias = false);

    public function hasWhere();

    public function bindWhere();

    public function truncate();

    public function getRefMap();

    public function getDb();

    public function setStatement($statement);
}
