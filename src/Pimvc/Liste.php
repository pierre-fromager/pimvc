<?php

/**
 * class liste
 * is a liste manager
 * 
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc;

use Pimvc\Views\Helpers\Toolbar\Glyph as helperToolbarGlyph;

class Liste {

    const DEFAULT_DELETE_ACION = '/delete';
    const DEFAULT_EDIT_ACION = '/edit';
    const LISTE_DEFAULT_PAGESIZE = 50;
    const LIST_SCRIPT_PARTIAL = 'listsearchscript.php';
    const LIST_SEARCH_ORDER = 'asc';
    const CLASS_ACTION = 'table-action';
    const PARAM_PAGESIZE = 'pagesize';
    const PARAM_PARENTHESIS = 'parenthesis';
    const PARAM_BREAK = '<br style="clear:both">';

    protected $_modelAdapter = '';

    protected $columns = null;
    protected $data = array();
    protected $controler = null;
    
    private $headers = '';
    private $labels = array();
    private $body = '';
    
    private $_model = null;
    private $_modelMapper = null;
    
    private $exclude = array();
    private $excludeAction = array();
    private $curentPage = false;
    private $filter = array();
    private $mandatory = array();
    
    protected $sql = '';
    
    private $booleanList = array();
    
    private $needAction = false;
    private $order = null;
    private $keyOrder = '';
    private $or = array();
    private $parenthesis = array();
    private $actionCondition = array();
    private $content;
    
    private $formaters = array();
    private $isFormated = false;

    private $usePaging = true;
    private $showSql = false;
    private $casts = array();
    
    private $actionPrefix;
    private $actionSuffix;

    /**
     * @see __construct
     * 
     * @param string $modelName
     * @param string $controler
     * @param array $exclude
     * @param array $excludeAction
     * @param int $curentPage
     * @param array $filter 
     */
    public function __construct(
        $modelName
        , $controler = 'user'
        , $exclude = array()
        , $excludeAction = array()
        , $curentPage = 0
        , $filter = array()
        , $mandatory = array()
        , $options = array()
    )
    {
        $this->content = '';
        $this->isFormated = false;
        $this->mandatory = $mandatory;
        $this->filter = $filter;
        $this->curentPage = $curentPage;
        $this->modelName = $modelName;
        $modelClass = $modelName;
        $this->modelConfig = \Pimvc\App::getInstance()->getConfig()->getSettings('dbPool');
        $this->_model = new $modelClass($this->modelConfig);
        $this->_modelAdapter = $this->_model->getAdapter();
        $this->_modelMapper = $this->_model->getDomainInstance();
        $this->booleanList = ($this->is4dDb()) 
            ? $this->_modelMapper->getBooleans() 
            : $this->booleanList;
        $this->controler = $controler;
        $this->exclude = $exclude;
        $this->excludeAction = $excludeAction;
        foreach ($this->excludeAction as $key => $value) {
            if ($value) $this->needAction = true;
        }
        if (isset($options['or']) && !empty($options['or'])) {
            $this->or = $options['or'];
            $this->_model->setOr($this->or);
        }
        if (isset($options[self::PARAM_PARENTHESIS]) && !empty($options[self::PARAM_PARENTHESIS])) {
            $this->parenthesis = $options[self::PARAM_PARENTHESIS];
            $this->_model->setParenthesis($this->parenthesis);
        }
        $this->order = (isset($options['order'])) 
            ? $options['order'] 
            : self::LIST_SEARCH_ORDER;
        $this->keyOrder = (isset($options['keyOrder']))
            ? $options['keyOrder'] 
            : '';
        $this->casts = (isset($options['casts'])) 
            ? $options['casts'] 
            : array();
        $this->_model->setCasts($this->casts);
        $this->setData();
    }
    
    /**
     * setActionPrefix
     * 
     * @param string $prefix 
     */
    public function setActionPrefix($prefix = '') {
        $this->actionPrefix = $prefix;
    }
    
    /**
     * setActionSuffix
     * 
     * @param string $suffix 
     */
    public function setActionSuffix($suffix = '') {
        $this->actionSuffix = $suffix;
    }
    
    /**
     * is4dDb
     * 
     * @return boolean 
     */
    private function is4dDb() {
        return ($this->_modelAdapter == \Pimvc\Db\Model\Orm::MODEL_ADAPTER_4D);
    }
    
    /**
     * setKeyOrder
     * 
     * @param type $key
     * @param type $order 
     */
    protected function setKeyOrder($key,$order) {
        $this->keyOrder = $key;
        $this->order = $order;
    }


    /**
     * setData sets data to be processed
     */
    protected function setData() {
        if (!$this->mandatory) {
            $mi = $this->_model->getDomainInstance();
            $this->columns = (!empty($this->exclude)) 
                ? array_diff($mi->getVars(), $this->exclude) 
                : $mi->getVars();
            unset($mi);
        } else {
            $this->columns = $this->mandatory;
        }
        $where = count($this->filter) ? $this->filter : array();
        $keyOrder = (empty($this->keyOrder)) 
            ? $this->_model->getPrimary() 
            : $this->keyOrder;
        $order = array($keyOrder => $this->order);
        $pagesize = Tools\Session::has(self::PARAM_PAGESIZE) 
            ? Tools\Session::get(self::PARAM_PAGESIZE) 
            : self::LISTE_DEFAULT_PAGESIZE;
        
        $limit = array($pagesize, $this->curentPage * $pagesize);
        $this->_model->find($this->columns, $where, $order, $limit);
        $this->sql = $this->_model->getSql();
        
        $this->data = $this->_model->getRowsetAsArray();
        $this->formatHelpers();
    }

    /**
     * formatColon
     * 
     * @param type $colon
     * @param type $helper 
     */
    private function formatColon($colon, $helper) {
        if (strpos($helper, '::') !== false) {
            list($class, $dummy, $method) = explode(':', $helper);
            if (method_exists($class, $method)) {
                $isStatic = $this->isStaticMethod($class, $method);
                $helperInstance = ($isStatic) ? null : new $class;
                foreach ($this->data as $key => $values) {
                    foreach ($values as $k => $v) {
                        if ($colon == $k) {
                            $this->data[$key][$k] = ($isStatic) 
                                ? call_user_func_array(
                                    $helper
                                    , array($this->data[$key][$k])
                                ) 
                                : $helperInstance->{$method}($this->data[$key][$k]);
                        }
                    }
                }
                unset($helperInstance);
            }
        }
    }
    
    /**
     * isStaticMethod
     * 
     * @param string $class
     * @param string $method
     * @return boolean 
     */
    private function isStaticMethod($class, $method) {
        $reflex = new ReflectionMethod($class, $method);
        $isStatic = ($reflex->isStatic());
        unset($reflex);
        return $isStatic;
    }

    /**
     * formatHelpers
     * 
     */
    protected function formatHelpers() {
        if ($this->formaters) {
            foreach ($this->formaters as $colon => $helper) {
                $this->formatColon($colon, $helper);
            }
        }
    }

    /**
     * setFormater
     * 
     * @param string $key
     * @param string $helperName 
     */
    public function setFormater($key, $helperName) {
        $this->formaters[$key] = $helperName; 
    }
    
    /**
     * setFormaters
     * 
     * @param array $formaters
     */
    public function setFormaters($formaters) {
        $this->formaters = $formaters; 
    }

    /**
     * setUsePaging
     * 
     * @param boolean $usage 
     */
    public function setUsePaging($usage) {
        $this->usePaging = $usage;
    }
    
    /**
     * setShowSql
     * 
     * @param boolean $usage 
     */
    public function setShowSql($enable) {
        $this->showSql = $enable;
    }

    /**
     * getHeaders returns headers as string
     * 
     * @return string 
     */
    private function getHeaders() {
        $headers = '';
        $rowLabels = array_combine($this->columns, $this->columns);
        foreach ($rowLabels as $key => $label) {
            $label = (isset($this->labels[$key])) ? $this->labels[$key] : $key;
            $headers .= '<th id="column_' . $key . '" class="theader">'
                . mb_strtoupper($label)
                . '</th>';
        }
        $headers .= '<th class="theader action">ACTIONS</th>';
        return '<thead>' . '<tr>' . $headers . '</tr>' . '</thead>' . PHP_EOL;
    }
    
    /**
     * setLabel
     * 
     * @param string $name
     * @param string $value 
     */
    public function setLabel($name, $value) {
        $this->labels[$name] = $value;
    }
    
    /**
     * setLabels
     * 
     * @param array $name
     * @param array $value 
     */
    public function setLabels($labels) {
        $this->labels = $labels;
    }


    /**
     * getCommandes returns toolbar for editing
     * 
     * @param int $id
     * @return string 
     */
    private function getCommandes($line) {
        $id = strtolower($this->_model->getPrimary());
        $idValue = $line[$id];
        $commandes = (string) new helperToolbarGlyph(
            array('id' => $idValue)
            , $this->controler
            , $this->excludeAction
            , $this->actionPrefix
            , $this->actionSuffix
        );
        return '<td class="' . self::CLASS_ACTION . '">' . $commandes . '</td>';
    }

    /**
     * getLines returns tr table body as string
     */
    private function getLines() {
        $this->body = '';
        foreach ($this->data as $lines) {
            $this->body .= '<tr>';
            foreach ($lines as $key => $value) {
                if (!in_array($key, $this->exclude)) {
                    $value = (is_array($value)) 
                        ? implode(' , ',$value) 
                        : $value;
                    $value = (empty($value) || is_null($value)) 
                        ? '-' 
                        : $value;
                    $this->body .= '<td>' . $value . '</td>';
                }
            }
            if ($this->needAction) {
                $excludeAction = $this->excludeAction;
                if ($this->actionCondition) {
                    foreach ($this->actionCondition as $conditionk => $conditionv) {
                        $key = $conditionv['key'];
                        $value = $conditionv['value'];
                        $operator = (isset($conditionv['operator'])) 
                            ? $conditionv['operator'] 
                            : '==';
                        $evalString = "return '" . $lines[$key] . "'"
                            . $operator . "'" . $value . "';";
                        $hasCondition = eval($evalString);
                        if ($hasCondition) {
                            $newCondition = array($conditionk => true);
                            $this->excludeAction = array_merge(
                                $this->excludeAction
                                , $newCondition
                            );
                        } 
                    }
                }
                $this->body .= $this->getCommandes($lines);
                $this->excludeAction = $excludeAction;               
            }
            $this->body .= '</tr>' . "\n";
        }
    }

    /**
     * getCaption returns table caption
     * 
     * @return string 
     */
    private function getCaption(){
        return '<caption>' . $this->modelName . '</caption>';
    }

    /**
     * getBody
     * 
     * @param array $options
     * @return string 
     */
    private function getBody($options = array()) {
        return (string) new Html\Element\Decorator(
            'tbody'
            , $this->body
            , $options
        );
    }

    /**
     * getTable returns table as string
     * 
     * @return string 
     */
    protected function getTable() {
        $defaultClasses = array(
            'managetable'
            , 'table'
            , 'table-condensed'
            , 'table-hover' 
            , 'table-stripped'
            , 'col-sm-12'
        );
        $tableOptions = array(
            'id' => 'table_' . md5($this->modelName)
            , 'class' => implode(' ', $defaultClasses)
        );
        $table = (string) new Html\Element\Decorator(
            'table'
            , $this->getHeaders() . $this->body
            , $tableOptions
        ) . self::PARAM_BREAK;
        return '<div class="table-responsive">' . $table . '</div>';
    }

    /**
     * render
     * 
     */
    public function render() {
        $this->formatHelpers();
        $this->headers = $this->getHeaders();
        $this->getLines();
        $this->body = $this->getBody();
        $this->_model->setOr($this->or);
        $this->_model->setParenthesis($this->parenthesis);
        $modelSize = $this->_model->counter($this->filter);
        $paging = ($this->usePaging) ? $this->getPaging($modelSize) : '';
        $this->content = $this->getSql() . $paging ;
        $this->content .= $this->getTable() . $this->getScript();
    }
    
    /**
     * getSql
     * 
     * @return string 
     */
    private function getSql() {
        return ($this->showSql) ? '<pre>' . $this->sql .'</pre>' : '';
    }


    /**
     * getData
     * 
     * @return array 
     */
    public function getData() {
        return $this->data;
    }
        
    /**
     * getJson
     * 
     * @return string 
     */
    public function getJson() {
        $alias = $this->_model->getAlias();
        $jsonList = new stdClass();
        $jsonList->liste = new stdClass();
        $jsonList->liste->alias = $alias;
        $jsonList->liste->filters = $this->filter;
        $jsonList->liste->columns = $this->columns;
        $jsonList->liste->pagesize = $this->getPageSize();
        $rowset = $this->_model->getRowset();
        $reducedDatas = array();
        if ($rowset) {
            foreach ($rowset as $set) {
                $reducedDatas[] = array_intersect_key(
                    get_object_vars($set)
                    , array_combine($this->columns, $this->columns)
                );
            }
        }
        $jsonList->liste->counter = $this->_model->counter($this->filter);
        $jsonList->liste->results = $reducedDatas;      
        $phpV = substr(phpversion(), 0, 3);
        $jsonOption = ($phpV > '5.3') ? JSON_PRETTY_PRINT : JSON_NUMERIC_CHECK;
        return json_encode($jsonList, $jsonOption);
    }
        
    /**
     * getPaging
     * 
     * @param int $modelSize
     * @return string 
     */
    private function getPaging($modelSize) {
        $pageSize = $this->getPageSize();
        $maxPage = ceil($modelSize / $pageSize);
        $navPaging = (string) new Views\Helpers\Paginator(
            $this->controler
            , $this->curentPage
            , $pageSize
            , $maxPage
        );
        $urlCombo = Tools\Session::getBaseUrl() . '/' . $this->controler 
            . DIRECTORY_SEPARATOR . self::PARAM_PAGESIZE . DIRECTORY_SEPARATOR;
        $comboPage = Views\Helpers\Pagesize::getCombo($urlCombo, $pageSize);
        return $navPaging 
            . '<div class="items-par-page">' 
            . $modelSize . ' résultat(s), '. $comboPage . ' items/page' 
            . '</div>';
    }
        
    /**
     * getPageSize
     * 
     * @return int 
     */
    private function getPageSize() {
        return (Tools\Session::has(self::PARAM_PAGESIZE)) 
            ? Tools\Session::get(self::PARAM_PAGESIZE) 
            : self::LISTE_DEFAULT_PAGESIZE;
    }
    
    /**
     * setActionCondition
     * 
     * @param array $conditions 
     */
    public function setActionCondition($conditions) {
        $this->actionCondition = $conditions;
    }
    
    /**
     * __toString returns table as string
     * 
     * @return string 
     */
    public function __toString() {       
        return (string) $this->content;
    }

    /**
     * getImgLink
     * 
     * @param string $name
     * @return string 
     */
    protected function getImgLink($name) {
        $baseUrl = Tools\Session::getBaseUrl();
        $imgPath = $baseUrl . 'public/images/arrow/' . $name;
        return '<img src="' . $imgPath . '" alt="Fields actions"/>';
    }

    /**
     * getScript
     * 
     */
    protected function getScript() {
        $templatePath = __DIR__ . '/Views/Helpers/Template/' 
            . self::LIST_SCRIPT_PARTIAL;
        if (!file_exists($templatePath)) {
            echo 'Missing file : ' . $templatePath;
            die;
        }
        $view = new View();
        $view->setParams([])->setFilename($templatePath)->render();
        return (string) $view;
    }


    /**
     * @see __destruct
     * 
     */
    public function __destruct() {
        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }

}
