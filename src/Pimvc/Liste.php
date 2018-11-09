<?php

/**
 * Pimvc\Liste
 *
 * is a liste manager
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc;

use Pimvc\Views\Helpers\Toolbar\Glyph as helperToolbarGlyph;

class Liste implements Interfaces\Liste
{
    protected $_modelAdapter = '';
    protected $columns = null;
    protected $data = [];
    protected $controler = null;
    protected $modelName = null;
    private $headers = '';
    private $labels = [];
    private $body = '';
    private $_model = null;
    private $_modelMapper = null;
    private $exclude = [];
    private $excludeAction = [];
    private $curentPage = false;
    private $filter = [];
    private $mandatory = [];
    protected $sql = '';
    private $needAction = false;
    private $order = null;
    private $keyOrder = '';
    private $or = [];
    private $parenthesis = [];
    private $actionCondition = [];
    private $content;
    private $formaters = [];
    private $isFormated = false;
    private $usePaging = true;
    private $showSql = false;
    private $casts = [];
    private $actionPrefix;
    private $actionSuffix;
    private $baseUrl;

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
        $modelName,
        $controler = 'user/manage',
        $exclude = [],
        $excludeAction = [],
        $curentPage = 0,
        $filter = [],
        $mandatory = [],
        $options = []
    ) {
        $this->baseUrl = \Pimvc\App::getInstance()->getRequest()->getBaseUrl();
        $this->content = '';
        $this->isFormated = false;
        $this->mandatory = $mandatory;
        $this->filter = $filter;
        $this->curentPage = (int) $curentPage;
        $this->modelName = $modelName;
        $this->modelConfig = \Pimvc\App::getInstance()->getConfig()->getSettings('dbPool');
        if (is_string($this->modelName)) {
            $modelClass = $modelName;
            $this->_model = new $modelClass($this->modelConfig);
        } elseif ($this->modelName instanceof \Pimvc\Db\Model\Orm) {
            $this->_model = $this->modelName;
        } else {
            throw new \Exception('Invalid model given');
        }
        $this->_modelAdapter = $this->_model->getAdapter();
        $this->_modelMapper = $this->_model->getDomainInstance();
        $this->controler = $controler;
        $this->exclude = $exclude;
        $this->excludeAction = $excludeAction;
        foreach ($this->excludeAction as $key => $value) {
            if ($value) {
                $this->needAction = true;
            }
        }
        if (isset($options['or']) && !empty($options['or'])) {
            $this->or = $options['or'];
            $this->_model->setOr($this->or);
        }
        if (isset($options[self::PARAM_PARENTHESIS]) && !empty($options[self::PARAM_PARENTHESIS])) {
            $this->parenthesis = $options[self::PARAM_PARENTHESIS];
            $this->_model->setParenthesis($this->parenthesis);
        }
        $this->order = (isset($options[self::PARAM_ORDER]))
            ? $options[self::PARAM_ORDER]
            : self::LIST_SEARCH_ORDER;
        $this->keyOrder = (isset($options[self::PARAM_K_ORDER]))
            ? $options[self::PARAM_K_ORDER]
            : '';
        $this->casts = (isset($options[self::PARAM_CASTS]))
            ? $options[self::PARAM_CASTS]
            : [];
       
        if ($this->modelName) {
            $this->_model->setCasts($this->casts);
            $this->setData();
        }

        return $this;
    }
    
    /**
     * setActionPrefix
     *
     * @param string $prefix
     */
    public function setActionPrefix($prefix = '')
    {
        $this->actionPrefix = $prefix;
        return $this;
    }
    
    /**
     * setActionSuffix
     *
     * @param string $suffix
     */
    public function setActionSuffix($suffix = '')
    {
        $this->actionSuffix = $suffix;
        return $this;
    }

    /**
     * setKeyOrder
     *
     * @param type $key
     * @param type $order
     */
    protected function setKeyOrder($key, $order)
    {
        $this->keyOrder = $key;
        $this->order = $order;
        return $this;
    }

    /**
     * setData sets data to be processed
     */
    protected function setData()
    {
        if (!$this->mandatory) {
            $mi = $this->_model->getDomainInstance();
            $this->columns = (!empty($this->exclude))
                ? array_diff($mi->getVars(), $this->exclude)
                : $mi->getVars();
            unset($mi);
        } else {
            $this->columns = $this->mandatory;
        }
        $where = count($this->filter) ? $this->filter : [];
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
        return $this;
    }
    
    /**
     * formatColon
     *
     * @param type $colon
     * @param type $helper
     */
    private function formatColon($colon, $helper)
    {
        if ($this->isValidFormater($helper)) {
            foreach ($this->data as $key => $values) {
                foreach ($values as $k => $v) {
                    if ($colon == $k) {
                        $this->data[$key][$k] = $helper::getStatic($this->data[$key][$k]);
                    }
                }
            }
        }
    }

    /**
     * isValidFormater
     *
     * @param Helper\Format\Interfaces\Liste $helper
     * @return boolean
     */
    private function isValidFormater($helper)
    {
        return class_exists($helper)
            && in_array(
                Helper\Format\Interfaces\Liste::class,
                class_implements($helper)
            );
    }

    /**
     * formatHelpers
     *
     */
    protected function formatHelpers()
    {
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
    public function setFormater($key, $helperName)
    {
        $this->formaters[$key] = $helperName;
        return $this;
    }
    
    /**
     * setFormaters
     *
     * @param array $formaters
     */
    public function setFormaters($formaters)
    {
        $this->formaters = $formaters;
        return $this;
    }

    /**
     * setUsePaging
     *
     * @param boolean $usage
     */
    public function setUsePaging($usage)
    {
        $this->usePaging = $usage;
        return $this;
    }
    
    /**
     * setShowSql
     *
     * @param boolean $enable
     */
    public function setShowSql($enable)
    {
        $this->showSql = $enable;
        return $this;
    }

    /**
     * getHeaders returns headers as string
     *
     * @return string
     */
    private function getHeaders()
    {
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
    public function setLabel($name, $value)
    {
        $this->labels[$name] = $value;
        return $this;
    }
    
    /**
     * setLabels
     *
     * @param array $name
     * @param array $value
     */
    public function setLabels($labels)
    {
        $this->labels = $labels;
        return $this;
    }

    /**
     * getCommandes
     *
     * @param type $line
     * @return type
     */
    private function getCommandes($line)
    {
        $id = strtolower($this->_model->getPrimary());
        $idValue = $line[$id];
        $commandes = new helperToolbarGlyph(
            [self::PARAM_ID => $idValue],
            $this->controler,
            $this->excludeAction,
            $this->actionPrefix,
            $this->actionSuffix
        );
        $stringCommande = (string) $commandes;
        unset($commandes);
        return '<td class="' . self::CLASS_ACTION . '">' . $stringCommande . '</td>';
    }

    /**
     * getLines returns tr table body as string
     */
    private function getLines()
    {
        $this->body = '';
        foreach ($this->data as $lines) {
            $this->body .= '<tr>';
            foreach ($lines as $key => $value) {
                if (!in_array($key, $this->exclude)) {
                    $value = (is_array($value))
                        ? implode(' , ', $value)
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
                        if (is_callable($value)) {
                            $hasCondition = $value($lines[$key]);
                        } else {
                            $evalString = "return '" . $lines[$key] . "'" . $operator . "'" . $value . "';";
                            $hasCondition = eval($evalString);
                        }
                        if ($hasCondition) {
                            $newCondition = array($conditionk => true);
                            $this->excludeAction = array_merge(
                                $this->excludeAction,
                                $newCondition
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
    private function getCaption()
    {
        return '<caption>' . $this->modelName . '</caption>';
    }

    /**
     * getBody
     *
     * @param array $options
     * @return string
     */
    private function getBody($options = [])
    {
        return (string) new Html\Element\Decorator(
            'tbody',
            $this->body,
            $options
        );
    }

    /**
     * getTable returns table as string
     *
     * @return string
     */
    protected function getTable()
    {
        $defaultClasses = [
            'managetable'
            , 'table'
            , 'table-condensed'
            , 'table-hover'
            , 'table-stripped'
            , 'col-sm-12'
        ];
        $modelName = is_string($this->modelName) ? $this->modelName : get_class($this->modelName);
        $tableOptions = [
            self::PARAM_ID => 'table_' . md5($modelName)
            , 'class' => implode(' ', $defaultClasses)
        ];
        $table = (string) new Html\Element\Decorator(
            'table',
            $this->getHeaders() . $this->body,
            $tableOptions
        ) . self::PARAM_BREAK;
        return '<div class="table-responsive">' . $table . '</div>';
    }

    /**
     * render
     *
     */
    public function render()
    {
        $this->formatHelpers();
        $this->headers = $this->getHeaders();
        $this->getLines();
        $this->body = $this->getBody();
        $this->_model->setOr($this->or);
        $this->_model->setParenthesis($this->parenthesis);
        $modelSize = $this->_model->counter($this->filter);
        $paging = ($this->usePaging) ? $this->getPaging($modelSize) : '';
        $this->content = $this->getSql() . $paging . $this->getTable();
        \Pimvc\Views\Helpers\Collection\Js::add('/public/js/liste.js');
        \Pimvc\Views\Helpers\Collection\Js::save();
        return $this;
    }
    
    /**
     * getSql
     *
     * @return string
     */
    private function getSql()
    {
        return ($this->showSql) ? '<pre>' . $this->sql . '</pre>' : '';
    }

    /**
     * getData
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
        
    /**
     * getJson
     *
     * @return string
     */
    public function getJson()
    {
        $alias = $this->_model->getAlias();
        $jsonList = new \stdClass();
        $jsonList->liste = new \stdClass();
        $jsonList->liste->alias = $alias;
        $jsonList->liste->filters = $this->filter;
        $jsonList->liste->columns = $this->columns;
        $jsonList->liste->pagesize = $this->getPageSize();
        $rowset = $this->_model->getRowset();
        $reducedDatas = [];
        if ($rowset) {
            foreach ($rowset as $set) {
                $reducedDatas[] = array_intersect_key(
                    get_object_vars($set),
                    array_combine($this->columns, $this->columns)
                );
            }
        }
        $jsonList->liste->counter = $this->_model->counter($this->filter);
        $jsonList->liste->results = $reducedDatas;
        return $jsonList;
    }
        
    /**
     * getPaging
     *
     * @param int $modelSize
     * @return string
     */
    private function getPaging($modelSize)
    {
        $pageSize = $this->getPageSize();
        $maxPage = ceil($modelSize / $pageSize);
        $navPaging = (string) new Views\Helpers\Paginator(
            $this->controler,
            $this->curentPage,
            $pageSize,
            $maxPage
        );
        $urlCombo = $this->baseUrl . '/' . $this->controler . '/' .
            self::PARAM_PAGESIZE . '/';
        $comboPage = Views\Helpers\Pagesize::getCombo($urlCombo, $pageSize);
        return $navPaging
            . '<div class="items-par-page">'
            . $modelSize . ' résultat(s), ' . $comboPage . ' items/page'
            . '</div>';
    }
        
    /**
     * getPageSize
     *
     * @return int
     */
    private function getPageSize()
    {
        return (Tools\Session::has(self::PARAM_PAGESIZE))
            ? Tools\Session::get(self::PARAM_PAGESIZE)
            : self::LISTE_DEFAULT_PAGESIZE;
    }
    
    /**
     * setActionCondition
     *
     * @param array $conditions
     */
    public function setActionCondition($conditions)
    {
        $this->actionCondition = $conditions;
        return $this;
    }
    
    /**
     * __toString returns table as string
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->content;
    }

    /**
     * getImgLink
     *
     * @param string $name
     * @return string
     */
    protected function getImgLink($name)
    {
        $imgPath = $this->baseUrl . '/public/images/arrow/' . $name;
        return '<img src="' . $imgPath . '" alt="Fields actions"/>';
    }

    /**
     * @see __destruct
     *
     */
    public function __destruct()
    {
        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }
}
