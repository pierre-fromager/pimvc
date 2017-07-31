<?php

/**
 * Description of Pimvc\Helper\Format\Interfaces\Db
 *
 * @author pierrefromager
 */

namespace Pimvc\Helper\Format\Interfaces;

interface Db {

    const Model_Expiraton = 30000;
    const Model_Value_Unknown = 'Unknown';
    const LINK_CLASS = 'format-link';
    const PARAM_ID = 'id';
    const DB_POOL = 'dbPool';
    const SEARCH_IN = '#IN';
    const O_BRACE = '(';
    const C_BRACE = ')';
    const COMA = ',';

    public function __construct();
    public static function getInstance();
    public static function getStatic($value);
    public function getData();
    public function get($key);
    public function setLink($link);
}
