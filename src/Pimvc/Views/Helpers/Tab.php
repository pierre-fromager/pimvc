<?php

/**
 * Description of Helper_Tabs
 * 
 * Convert associative array considering 
 * key as tab headers and value as tab content
 *
 * @author pierrefromager
 */

namespace Pimvc\Views\Helpers;

class Tab {
    
    const TAB_ID_PREFIX = 'tab-';
    const TAB_CLASS = 'tabs';
    const TAB_ITEM_CLASS = 'tabs-item';
    
    protected $headers = [];
    protected $content = [];   
    private  $data = null;
    protected $id = '';
    protected $html = '';
    protected $selected = '';

    /**
     * __construct
     * 
     * @param array $datas 
     */
    public function __construct($datas, $selected = '') {
        $this->data = $datas;
        $this->headers = array_keys($datas);
        $this->content = $datas;
        $this->selected = $selected;
        $this->id = self::TAB_ID_PREFIX . rand(2783783, 3783783);
    }
    
    /**
     * setId
     * 
     * @param string $id 
     */
    public function setId($id) {
        $this->id = $id;
    }
    
    /**
     * render
     * 
     */
    public function render() {
        $this->html = $this->getBlock(
            $this->id
            , self::TAB_CLASS
            , $this->getList($this->getTitle($this->headers)) 
                . $this->getBody($this->headers)
        ) . $this->getScript();
    }
    
    /**
     * __toString
     * 
     * @return string 
     */
    public function __toString() {
        return (string) $this->html;;
    }
    
    /**
     * @see __destruct
     */
    public function __destruct() {
        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }
    
    /**
     * getTitle
     * 
     * @param array $headers
     * @return string 
     */
    private function getTitle($headers) {
        $titles = '';
        foreach ($headers as $header) {
            $titles .= $this->getItem($header);
        }
        return $titles;
    }
    
    /**
     * getBody
     * 
     * @param array $headers
     * @return string 
     */
    private function getBody($headers) {
        $body = '';
        foreach ($headers as $header) {
            $body .= $this->getBlock(
                md5($header)
                , self::TAB_ITEM_CLASS
                , $this->getPara($this->data[$header])
            );
        }
        return $body;
    }
    
    /**
     * getBlock
     * 
     * @param string $id
     * @param string $class
     * @param string $content
     * @return string 
     */
    private function getBlock($id, $class, $content) {
        return '<div'
            . ' id="' . $id . '"'
            . ' class="' . $class . '">' . PHP_EOL
            . $content 
            . '</div>' . PHP_EOL;
    }
    
    /**
     * getList
     * 
     * @param string $content
     * @return string 
     */
    private function getList($content) {
        return $this->getTag('ul', $content);
    }

    /**
     * getItem
     * 
     * @param string $item
     * @return string 
     */
    private function getItem($item) {     
        return $this->getTag('li', $this->getLink($item));
    }


    /**
     * getLink
     * 
     * @param string $item
     * @return string 
     */
    private function getLink($item) {
        return '<a'
            . ' href="#' . md5($item) . '"'
            . '>' 
            . $item 
            . '</a>';
    }
    
    /**
     * getPara
     * 
     * @param string $content
     * @return string 
     */
    private function getPara($content) {
        return $this->getTag('p', $content);
    }

    /**
     * getTag
     * 
     * @param string $tag
     * @param string $content
     * @return string 
     */
    private function getTag($tag, $content) {
        return '<' . $tag . '>' 
            . $content 
            . '</' . $tag . '>';
    }

    /**
     * getScript
     * 
     * @return string 
     */
    private function getScript() {
        $hashSelected = md5($this->selected);
        $selected = ((empty($this->selected))) 
            ? ''
            : 'var indexTab = $(\'#' . $this->id 
                . ' a[href="#'.$hashSelected.'"]\').parent().index();'
                . '$("#'.$this->id.'").tabs("select", indexTab);'  ;
        $script = '<script type="text/javascript">'
            .' $(function() {'
                . '$("#'.$this->id.'").tabs();'
                . $selected
            . '});'
            . '</script>';
        return $script;
    }
}

