<?php

/**
 * Description of Helper_Widget
 *
 * @author pierrefromager
 */

namespace Pimvc\Views\Helpers;

use Pimvc\Html\Element\Decorator as Deco;

abstract class Widget implements Interfaces\Widget
{
    protected $content;
    protected $sectionOptions;
    protected $sectionDecorator;
    protected $headerDecorator;
    protected $headerOptions;
    protected $title;
    protected $titleOptions;
    protected $titleDecorator;
    protected $body;
    protected $bodyDecorator;
    protected $bodyOtions;
    protected $bodyOptions;
    protected $footer;
    protected $footerDecorator;
    protected $footerOtions;

    /**
     * __construct
     *
     * @param string $title
     * @param string $body
     */
    public function __construct()
    {
        return $this;
    }

    /**
     * render
     *
     */
    public function render()
    {
        $this->content = $this->getSection(
            $this->getHeader($this->title)
                . $this->getBody($this->body)
                . $this->getFooter()
        );
    }

    /**
     * setTitleOptions
     *
     * @param array $options
     */
    public function setTitleOptions($options = [])
    {
        $this->titleOptions = $options;
        return $this;
    }

    /**
     * setTitleDecorator
     *
     * @param string $decorator
     */
    public function setTitleDecorator($decorator = self::TITLE_DECORATOR)
    {
        $this->titleDecorator = $decorator;
        return $this;
    }

    /**
     * setBodyDecorator
     *
     * @param string $decorator
     */
    public function setBodyDecorator($decorator = self::BODY_DECORATOR)
    {
        $this->bodyDecorator = $decorator;
        return $this;
    }

    /**
     * setBodyOptions
     *
     * @param array $options
     */
    public function setBodyOptions($options = [])
    {
        $this->bodyOptions = $options;
        return $this;
    }

    /**
     * setBody
     *
     * @param string $body
     */
    public function setBody($body = '')
    {
        $this->body = $body;
        return $this;
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
     * setSectionOptions
     *
     * @param array $options
     */
    public function setSectionOptions($options = [])
    {
        $this->sectionOptions = $options;
        return $this;
    }

    /**
     * setSectionDecorator
     *
     * @param string $decorator
     */
    public function setSectionDecorator($decorator)
    {
        $this->sectionDecorator = $decorator;
        return $this;
    }
    
    /**
     * setFooter
     *
     * @param string $footer
     * @return $this
     */
    public function setFooter($footer)
    {
        $this->footer = $footer;
        return $this;
    }
    
    /**
     * setFooterDecorator
     *
     * @param string $decorator
     */
    public function setFooterDecorator($decorator)
    {
        $this->footerDecorator = $decorator;
        return $this;
    }
    
    /**
     * setFooteOptions
     *
     * @param array $options
     */
    public function setFooteOptions($options)
    {
        $this->footerOtions = $options;
        return $this;
    }

    /**
     * setHeaderDecorator
     *
     * @param string $decorator
     * @return $this
     */
    public function setHeaderDecorator($decorator)
    {
        $this->headerDecorator = $decorator;
        return $this;
    }

    /**
     * setHeaderDecorator
     *
     * @param array $options
     * @return $this
     */
    public function setHeaderOptions($options)
    {
        $this->headerOptions = $options;
        return $this;
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->content;
    }

    /**
     * getSection
     *
     * @param string $content
     * @return string
     */
    private function getSection($content)
    {
        return ($content)
            ? (string) new Deco($this->sectionDecorator, $content, $this->sectionOptions)
            : '';
    }

    /**
     * getBody
     *
     * @return string
     */
    private function getBody()
    {
        return ($this->body)
            ? (string) new Deco($this->bodyDecorator, $this->body, $this->bodyOptions)
            : '';
    }

    /**
     * getHeader
     *
     * @param string $title
     * @return string
     */
    private function getHeader($title)
    {
        return ($title)
            ? (string) new Deco($this->headerDecorator, $this->formatedTitle($title), $this->headerOptions)
            : '';
    }
    
    /**
     * getFooter
     *
     * @return string
     */
    private function getFooter()
    {
        return ($this->footer)
            ? (string) new Deco($this->footerDecorator, $this->footer, $this->footerOtions)
            : '';
    }

    /**
     * formatedTitle
     *
     * @param string $title
     * @return string
     */
    private function formatedTitle($title)
    {
        return ($title) ?
            (string) new Deco($this->titleDecorator, $title, $this->titleOptions)
            : '';
    }

    /**
     * isValid
     *
     * @return boolean
     */
    protected function isValid()
    {
        return ($this->title && $this->body);
    }
}
