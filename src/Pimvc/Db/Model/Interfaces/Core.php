<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pimvc\Db\Model\Interfaces;

interface Core
{

    const MODEL_SELECT = 'SELECT ';
    const MODEL_DEBUG = true;
    const MODEL_TRACE = false;
    const MODEL_ADAPTER_PGSQL = 'Pdopgsql';
    const MODEL_ADAPTER_SQLITE = 'Pdosqlite';
    const MODEL_ADAPTER_4D = 'Pdo4d';
    const MODEL_ADAPTER_MYSQL = 'Pdomysql';
    const MODEL_ADAPTER_DEFAULT = self::MODEL_ADAPTER_MYSQL;
    const MODEL_FROM = ' FROM ';
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

    public function run($sql, $bindParams = [], $bindTypes = []);

    public function bindArray(\PDOStatement &$poStatement, &$paArray, $forcedTypes = []);
    
    public function describeTable($name = '');

    public function getQueryType($sql);

    public function getSql();

    public function getSize();

    public function tableExist($tablename);

    public function showTables();
}
