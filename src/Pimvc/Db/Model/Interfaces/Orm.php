<?php
/**
 * Description of domain
 *
 * @author pierrefromager
 */
namespace Pimvc\Db\Model\Interfaces;

interface Orm
{
  
    const MODEL_TRANS = ':TRANS_';
    const MODEL_OPERATOR_SPLITER = '#';
    const MODEL_DOMAIN_CLASS_PREFIX = '\Model\Domain\\';
    const MODEL_PARENTH_O = '(';
    const MODEL_PARENTH_C = ')';
    const MODEL_CACHE_SUFFIX = 'Db/Queries/';
    const MODEL_USE_CACHE = false;
    const MODEL_INDEX_FIELD = 'field';
    const MODEL_OPERATOR_TRIGGER = '[]%_';
    const MODEL_BETWEEN = ' BETWEEN ';
    const MODEL_DOMAIN = 'Domain';
    const BACKSLASH = '\\';
    const _LOCAL = 'local';
    const _FOREIGN = 'foreign';
    const _ALIAS = 'alias';
    const _TABLE = 'table';
    const _CARDINALITY = 'cardinality';

    public function __construct($config = []);

    public function setCast($fieldName, $typeCast);

    public function setCasts($fieldsCast);

    public function getName();

    public function getDefaultSchema();

    public function getAdapter();

    public function getStatement();

    public function getDomainInstance();

    public function getColumns();

    public function setFetchMode($mode);

    public function getFetchMode();

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

    public function getDiffDomainObject(\Pimvc\Db\Model\Domain $d1, \Pimvc\Db\Model\Domain $d2);

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

    public function update($params = []);

    public function directsql($sql, $params);

    public function delete();

    public function multidelete();

    public function insert($params = [], $forgetPrimary = true);

    public function getSum($column, $criterias = [], $precision = 2);

    public function getAvg($column, $criterias = [], $precision = 2);

    public function getMathFn($fn, $column, $criterias = [], $precision = 2, $precisionFn = 'round');

    public function run($sql, $bindParams = [], $bindTypes = []);

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
