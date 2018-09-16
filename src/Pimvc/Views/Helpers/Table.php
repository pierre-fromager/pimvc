<?php

/**
 * Helper_Table
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\Views\Helpers;

class Table
{
    const HELPER_TITLE_TAG = 'p';
    const HELPER_TABLE_TAG = 'table';
    const HELPER_BODY_TAG = 'tbody';
    const HELPER_HEAD_TAG = 'thead';
    const HELPER_TH_TAG = 'th';
    const HELPER_TD_TAG = 'td';
    const HELPER_TR_TAG = 'tr';
    const HELPER_TAG_GT = '>';
    const HELPER_TAG_LT = '<';
    const HELPER_TAG_LTC = '</';
    const HELPER_TAG_X = '><';
    const HELPER_CV = '" ';

    protected $titleOptions;
    protected $tableOptions;
    protected $theadOptions;
    protected $tbodyOptions;
    
    protected $title;
    protected $class;
    protected $titleClass;
    protected $titleStyle;
    protected $id;
    protected $header;
    protected $data;
    protected $content;

    /**
     * @see __construct
     *
     * @param string $title
     * @param array $header
     * @param array $data
     */
    public function __construct($title = '', $header = [], $data = [])
    {
        $this->titleOptions = [];
        $this->tableOptions = array(
            'id' => ''
            , 'class' => 'managetable table table-hover table-stripped col-sm-12'
        );
        $this->content = '';
        $this->setTitle($title);
        $this->setHeader($header);
        $this->pre();
        $this->setData($data);
        $this->post();
        $this->render();
        return $this;
    }

    /**
     *  pre
     */
    protected function pre()
    {
    }

    /**
     * post
     */
    protected function post()
    {
    }

    /**
     * setTitle
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
    
    /**
     * setId
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->tableOptions['id'] = $id;
        return $this;
    }

    /**
     * setClass
     *
     * @param string $class
     */
    public function setClass($class)
    {
        $this->tableOptions['class'] = $class;
        return $this;
    }
    
    /**
     * setTableOptions
     *
     * @param array $options
     */
    public function setTableOptions($options)
    {
        $this->tableOptions = $options;
        return $this;
    }

    /**
     * setTitleOptions
     *
     * @param string $class
     */
    public function setTitleOptions($options)
    {
        $this->titleOptions = $options;
        return $this;
    }

    /**
     * setHeader
     *
     * @param array $header
     */
    public function setHeader($header)
    {
        $this->header = $header;
        return $this;
    }

    /**
     * setData
     *
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
    
    /**
     * render
     *
     */
    public function render()
    {
        $this->content = $this->getFormatedTitle();
        $this->content .= $this->_getTable(
            $this->getFormatedHeader() . $this->getFormatedBody(),
            $this->tableOptions
        );
    }
    
    /**
     * @see __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->content;
    }
    
    /**
     * @see __destruct
     */
    public function __destruct()
    {
        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }
    
    /**
     * getTitle
     *
     * @return string
     */
    private function getFormatedTitle()
    {
        return (string) new \Pimvc\Html\Element\Decorator(
            self::HELPER_TITLE_TAG,
            $this->title,
            $this->titleOptions
        );
    }

    /**
     * getFormatedHeader
     *
     * @return string
     */
    private function getFormatedHeader()
    {
        $glue = self::HELPER_TAG_LTC . self::HELPER_TH_TAG . self::HELPER_TAG_X
            . self::HELPER_TH_TAG . self::HELPER_TAG_GT;
        return $this->_getThead(
            $this->_getTr(
                $this->_getTh(implode($glue, $this->header))
            )
        );
    }
    
    /**
     * _getTr
     *
     * @param string $text
     * @param array $options
     * @return string
     */
    private function _getTr($text, $options = [])
    {
        return $this->_getTag(self::HELPER_TR_TAG, $text, $options);
    }
    
    /**
     * _getTh
     *
     * @param string $text
     * @param array $options
     * @return string
     */
    private function _getTh($text, $options = [])
    {
        return $this->_getTag(self::HELPER_TH_TAG, $text, $options);
    }
    
    /**
     * _getTd
     *
     * @param string $text
     * @param array $options
     * @return string
     */
    private function _getTd($text, $options = [])
    {
        return $this->_getTag(self::HELPER_TD_TAG, $text, $options);
    }
    
    /**
     * _getTbody
     *
     * @param string $text
     * @param array $options
     * @return string
     */
    private function _getTbody($text, $options = [])
    {
        return $this->_getTag(self::HELPER_BODY_TAG, $text, $options);
    }
    
    /**
     * _getThead
     *
     * @param string $text
     * @param array $options
     * @return string
     */
    private function _getThead($text, $options = [])
    {
        return $this->_getTag(self::HELPER_HEAD_TAG, $text, $options);
    }
    
    /**
     * _getTable
     *
     * @param string $text
     * @param array $options
     * @return string
     */
    private function _getTable($text, $options = [])
    {
        return $this->_getTag(self::HELPER_TABLE_TAG, $text, $options);
    }
    
    /**
     * _getTag
     *
     * @param string $tag
     * @param string $text
     * @param array $options
     * @return string
     */
    private function _getTag($tag, $text, $options = [])
    {
        return (string) new \Pimvc\Html\Element\Decorator(
            $tag,
            $text,
            $options
        );
    }

    /**
     * getLines
     *
     * @return string
     */
    private function getLines()
    {
        $lines = '';
        $glue = self::HELPER_TAG_LTC . self::HELPER_TD_TAG . self::HELPER_TAG_X
            . self::HELPER_TD_TAG . self::HELPER_TAG_GT;
        foreach ($this->data as $line) {
            $tds = $this->_getTd(implode($glue, $line));
            $lines .= $this->_getTr($tds);
        }
        return $lines;
    }

    /**
     * getFormatedBody
     *
     * @return string
     */
    private function getFormatedBody()
    {
        return $this->_getTbody($this->getLines());
    }
}
