<?php

/**
 * Description of Pimvc\Interfaces\Liste
 *
 * @author pierrefromager
 */

namespace Pimvc\Interfaces;

interface Liste {

    const DEFAULT_DELETE_ACION = '/delete';
    const DEFAULT_EDIT_ACION = '/edit';
    const LISTE_DEFAULT_PAGESIZE = 50;
    const LIST_SCRIPT_PARTIAL = 'listsearchscript.php';
    const LIST_SEARCH_ORDER = 'asc';
    const CLASS_ACTION = 'table-action';
    const PARAM_PAGESIZE = 'pagesize';
    const PARAM_PARENTHESIS = 'parenthesis';
    const PARAM_BREAK = '<br style="clear:both">';

    public function __construct(
        $modelName
        , $controler = 'user'
        , $exclude = []
        , $excludeAction = []
        , $curentPage = 0
        , $filter = []
        , $mandatory = []
        , $options = []
    );

    public function setActionPrefix($prefix = '');

    public function setActionSuffix($suffix = '');

    public function setFormater($key, $helperName);

    public function setFormaters($formaters);

    public function setUsePaging($usage);

    public function setShowSql($enable);

    public function setLabel($name, $value);

    public function setLabels($labels);

    public function render();

    public function getData();

    public function getJson();

    public function setActionCondition($conditions);

    public function __toString();

    public function __destruct();
}
