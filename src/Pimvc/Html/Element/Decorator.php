<?php

/**
 * Description of Lib_Html_Element_Decorator
 *
 * @author pierrefromager
 */
namespace Pimvc\Html\Element;


class Decorator {

    const EMPTY_VAL = '';
    const LT = '<';
    const GT = '>';
    const SL = '/';

    protected $element;
    protected $text;
    protected $options;
    protected $render;
    protected $content;

    /**
     * __construct
     * 
     * @param string $element
     * @param type $text
     * @param array $options
     * @param boolean $render
     */
    public function __construct($element, $text, $options = [], $render = true) {
        $this->content = self::EMPTY_VAL;
        $this->setElement($element);
        $this->setText($text);
        $this->setOptions($options);
        $this->render = $render;
        $this->render();
    }
    
    /**
     * get
     * 
     * @return \Library\Html\Element\Decorator 
     */
    public function get() {
        return $this;
    }

    /**
     * render
     * 
     * @return string 
     */
    public function render() {
        if ($this->render) {
            $options = (empty($this->options)) 
                ? self::EMPTY_VAL 
                : $this->getOptions($this->options);
            $text = ($this->isDisplayText()) ? $this->text : self::EMPTY_VAL;
            $end = (empty($text) && ($this->isSelfClosing())) 
                ? self::SL . self::GT 
                : self::GT . $text . self::LT
                    . self::SL . $this->element . self::GT;
            $this->content = self::LT . $this->element . $options . $end;
            return $this->content;
        }
        return '';
    }
    /**
     * setElement
     * 
     * @param string $element 
     */
    public function setElement($element) {
        $this->element = $element;
    }
    
    /**
     * setOptions
     * s
     * @param array $options 
     */
    public function setOptions($options) {
        $this->options = (is_array($options)) ? $options : [];
    }
    
    /**
     * setText
     * 
     * @param string $text 
     */
    public function setText($text) {
        $this->text = $text;
    }
    
    /**
     * getText
     * 
     * @param string $text 
     */
    public function getText() {
        return $this->text;
    }

    /**
     * optionsArray
     * 
     * @return array 
     */
    public function optionsArray() {
        return array(
            'element' => $this->element
            , 'options' => $this->options
        );
    }
    
    /**
     * __toString

     * @return string 
     */
    public function __toString() {
        return (string) $this->content;
    }
    
    /**
     * __destruct
     * 
     */
    public function __destruct() {
        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }

    /**
     * isClosedException
     * 
     * @return boolean 
     */
    private function isSelfClosing() {
        $selfClosingTag = array(
            'area', 'base', 'br', 'col', 'command', 'embed', 'hr'
            , 'img', 'input', 'keygen', 'link', 'meta', 'param'
            , 'source', 'track', 'wbr'
        );
        $isSelfClosingTag = in_array($this->element, $selfClosingTag);
        return $isSelfClosingTag;
    }
    
    /**
     * isDisplayText
     * 
     * @return boolean 
     */
    private function isDisplayText() {
        return $this->element != 'input';
    }

    /**
     * formatOptions
     * 
     * @param array $options
     * @return string 
     */
    private function getOptions($options = []) {
        $formatedOptions = self::EMPTY_VAL;
        if (!empty($options)) {
            $render = [];
            foreach ($options as $key => $value) {
                $render[] = ' ' . $key . '="' . $value . '"';
            }
            $formatedOptions = implode($render, self::EMPTY_VAL);
            unset($render);
        }
        return $formatedOptions;
    }
}
