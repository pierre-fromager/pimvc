<?php
/**
 * Description of Pimvc\Db\Model\Interfaces\Forge
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Db\Model\Interfaces;

interface Forge
{

    const _DB_POOL = 'dbPool';
    const _ADAPTER = 'adapter';
    const _CREATE_TABLE = 'CREATE TABLE';
    const _TABLE = 'TABLE';
    const _CREATE = 'CREATE';
    const _ALTER = 'ALTER';
    const _RENAME = 'RENAME';
    const _TO = 'TO';
    const _VARCHAR = 'VARCHAR';
    const _INT = 'INT';
    const _FLOAT = 'FLOAT';
    const _UNSIGNED = 'UNSIGNED';
    const _AUTO_INCREMENT = 'AUTO_INCREMENT';
    const _PRIMARY_KEY = 'PRIMARY KEY';

    public function __construct($slot);

    public function tableRename($name, $newName);

    public function tableCreate($tableName, $columns);
}
