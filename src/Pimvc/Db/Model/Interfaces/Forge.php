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
    const _ADD = 'ADD';
    const _COLUMN = 'COLUMN';
    const _CREATE = 'CREATE';
    const _INDEX = 'INDEX';
    const _UNIQUE = 'UNIQUE';
    const _ALTER = 'ALTER';
    const _RENAME = 'RENAME';
    const _INSERT = 'INSERT';
    const _INTO = 'INTO';
    const _VALUES = 'VALUES';
    const _TO = 'TO';
    const _VARCHAR = 'VARCHAR';
    const _INT = 'INT';
    const _FLOAT = 'FLOAT';
    const _UNSIGNED = 'UNSIGNED';
    const _AUTO_INCREMENT = 'AUTO_INCREMENT';
    const _PRIMARY_KEY = 'PRIMARY KEY';

    public function __construct(string $slot);

    public function tableRename(string $name, string $newName);

    public function tableCreate(string $tableName, \Pimvc\Db\Model\Fields $columns, bool $withPk);
}
