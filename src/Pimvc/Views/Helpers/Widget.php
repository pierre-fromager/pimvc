<?php

/**
 * Description of Helper_Widget
 *
 * @author pierrefromager
 */

namespace Pimvc\Views\Helpers;

use Pimvc\Html\Element\Decorator as Deco;

abstract class Widget implements Interfaces\Widget {

    protected $title;
    protected $body;
    protected $content;
    protected $sectionOptions;
    protected $sectionDecorator;
    protected $headerDecorator;
    protected $headerOptions;
    protected $titleDecorator;
    protected $bodyDecorator;
    protected $bodyOtions;
    protected $titleOptions;
    protected $bodyOptions;

    /**
     * __construct
     * 
     * @param string $title
     * @param string $body 
     */
    public function __construct() {
        return $this;
    }

    /**
     * render
     * 
     */
    public function render() {
        $this->content = $this->getSection(
            $this->getHeader($this->title) . $this->getBody($this->body)
        );
    }

    /**
     * setTitleOptions
     * 
     * @param array $options 
     */
    public function setTitleOptions($options = []) {
        $this->titleOptions = $options;
        return $this;
    }

    /**
     * setTitleDecorator
     * 
     * @param string $decorator 
     */
    public function setTitleDecorator($decorator = self::TITLE_DECORATOR) {
        $this->titleDecorator = $decorator;
        return $this;
    }

    /**
     * setBodyDecorator
     * 
     * @param string $decorator 
     */
    public function setBodyDecorator($decorator = self::BODY_DECORATOR) {
        $this->bodyDecorator = $decorator;
        return $this;
    }

    /**
     * setBodyOptions
     * 
     * @param array $options 
     */
    public function setBodyOptions($options = []) {
        $this->bodyOptions = $options;
        return $this;
    }

    /**
     * setBody
     * 
     * @param string $body 
     */
    public function setBody($body = '') {
        $this->body = $body;
        return $this;
    }

    /**
     * setTitle
     * 
     * @param string $title 
     */
    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    /**
     * setSectionOptions
     * 
     * @param array $options 
     */
    public function setSectionOptions($options = []) {
        $this->sectionOptions = $options;
        return $this;
    }

    /**
     * setSectionDecorator
     * 
     * @param string $decorator 
     */
    public function setSectionDecorator($decorator) {
        $this->sectionDecorator = $decorator;
        return $this;
    }

    /**
     * setHeaderDecorator
     * 
     * @param string $decorator
     * @return $this
     */
    public function setHeaderDecorator($decorator) {
        $this->headerDecorator = $decorator;
        return $this;
    }

    /**
     * __toString
     * 
     * @return string 
     */
    public function __toString() {
        return (string) $this->content;
    }

    /**
     * getSection
     * 
     * @param string $content
     * @return string 
     */
    private function getSection($content) {
        return (string) new Deco($this->sectionDecorator, $content, $this->sectionOptions);
    }

    /**
     * getBody
     * 
     * @return string 
     */
    private function getBody() {
        return (string) new Deco($this->bodyDecorator, $this->body, $this->bodyOptions);
    }

    /**
     * getHeader
     * 
     * @param string $title
     * @return string 
     */
    private function getHeader($title) {
        return (string) new Deco(
            $this->headerDecorator, $this->getFormatedTitle($title), $this->headerOptions
        );
    }

    /**
     * getFormatedTitle
     * 
     * @param string $title
     * @return string 
     */
    private function getFormatedTitle($title) {
        return (string) new Deco($this->titleDecorator, $title, $this->titleOptions);
    }

    /**
     * isValid
     * 
     * @return boolean
     */
    protected function isValid() {
        return ($this->title && $this->body);
    }

}
