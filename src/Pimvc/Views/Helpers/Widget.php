<?php

/**
 * Description of Helper_Widget
 *
 * @author pierrefromager
 */
namespace Pimvc\Views\Helpers;

use Pimvc\Html\Element\Decorator as widgetDecorator;

class Widget {

    const PARAM_SECTION = 'section';
    const PARAM_HEADER = 'header';
    const PARAM_BODY = 'body';
    const PARAM_WIDGET = 'widget';
    const PARAM_CLASS = 'class';
    const TITLE_DECORATOR = 'h3';
    const BODY_DECORATOR = 'div';
    
    protected $title;
    protected $body;
    protected $content;
    
    protected $sectionOptions;
    protected $titleDecorator;
    protected $titleOptions;
    protected $bodyOptions;

    /**
     * __construct
     * 
     * @param string $title
     * @param string $body 
     */
    public function __construct($title, $body) {
        $this->content = '';
        $this->setTitle($title);
        $this->setBody($body);
        $this->setSectionOptions([]);
        $this->setTitleDecorator(self::TITLE_DECORATOR);
        $this->setTitleOptions([]);
        $this->setBodyOptions([]);
        $this->render();
    }
    
    /**
     * render
     * 
     */
    public function render() {
        $this->content = $this->getSection(
            $this->getHeader($this->title)
            . $this->getBody($this->body)              
        );
    }
    
    /**
     * setTitleOptions
     * 
     * @param array $options 
     */
    public function setTitleOptions($options) {
        $this->titleOptions = $options;
    }
    
    /**
     * setTitleDecorator
     * 
     * @param string $decorator 
     */
    public function setTitleDecorator($decorator) {
        $this->titleDecorator = $decorator;
    }
    
    /**
     * setBodyOptions
     * 
     * @param array $options 
     */
    public function setBodyOptions($options) {
        $this->bodyOptions = $options;
    }
    
    /**
     * setBody
     * 
     * @param array $options 
     */
    public function setBody($body) {
        $this->body = $body;
    }
    
    /**
     * setTitle
     * 
     * @param array $options 
     */
    public function setTitle($title) {
        $this->title = $title;
    }
    
    /**
     * setSectionOptions
     * 
     * @param array $options 
     */
    public function setSectionOptions($options) {
        $this->sectionOptions = $options;
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
        $sectionOptions = array(
            self::PARAM_CLASS => self::PARAM_WIDGET
        );
        $sectionOptions = array_merge($sectionOptions, $this->sectionOptions);
        $section = new widgetDecorator(
            self::PARAM_SECTION
            , $content
            , $sectionOptions
        );
        $section->render();
        $sectionContent = (string) $section;
        unset($section);
        return $sectionContent;
    }
    
    /**
     * getBody
     * 
     * @return string 
     */
    private function getBody() {
        $bodyOptions = array(
            self::PARAM_CLASS => self::PARAM_BODY
        );
        $bodyOptions = array_merge($bodyOptions, $this->bodyOptions);
        $body = new widgetDecorator(
            self::BODY_DECORATOR
            , $this->body
            , $bodyOptions
        );
        $body->render();
        $bodyContent = (string) $body;
        unset($body);
        return $bodyContent;
    }
    
    /**
     * getHeader
     * 
     * @param string $title
     * @return string 
     */
    private function getHeader($title) {
        $headerTitleContent = '';
        if ($title) {
            $formatedTitle = $this->getFormatedTitle($title);
            $header = new widgetDecorator(self::PARAM_HEADER, $formatedTitle);
            $header->render();
            $headerTitleContent = (string) $header;
            unset($header);
        }
        return $headerTitleContent;
    }
    
    /**
     * getFormatedTitle
     * 
     * @param string $title
     * @return string 
     */
    private function getFormatedTitle($title) {
        $formatedTitleContent = '';
        if ($title) {
            $formatedTitle = new widgetDecorator(
                $this->titleDecorator
                , $title
                , $this->titleOptions
            );
            $formatedTitle->render();
            $formatedTitleContent = (string) $formatedTitle;
            unset($formatedTitle);
        }
        return $formatedTitleContent;
    }

}

