<?php
/*
 * Pimvc\Db\Adapter\Interfaces\Adapter
 *
 */
namespace Pimvc\Db\Adapter\Interfaces;

interface Adapter
{

    const CHARSET = 'utf8';
    const PREFIX_MYSQL = 'mysql:';
    const HOST_PREFIX = 'host=';
    const DB_NAME_PREFIX = 'dbname=';
    const ERR_CON_FAIL = 'Connexion échouée : ';
    const _HOST = 'host';
    const _USER = 'user';
    const _FILE = 'file';
    const _PASSWORD = 'password';
    const _OPTIONS = 'options';
    const _NAME = 'name';
    const _PORT = 'port';

    public static function getInstance(array $params);
}
