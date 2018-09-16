<?php

/**
 * Description of Pimvc\Views\layout
 *
 * @author pierrefromager
 */

namespace Pimvc;

use \Pimvc\Interfaces\View as viewInterface;
use \Pimvc\Interfaces\Layout as layoutInterface;

class Layout extends \Pimvc\View implements viewInterface, layoutInterface
{
    protected $path;
    protected $layoutParams = [];
    protected $app;
    protected $name;

    /**
     * __construct
     *
     * @param string $name
     * @return $this
     */
    public function __construct()
    {
        parent::__construct();
        return $this;
    }
    
    /**
     * setName
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = ($name) ? $name : self::LAYOUT_DEFAULT_NAME;
        $this->path = $this->app->getPath() . self::LAYOUT_PATH
            .  $this->name . DIRECTORY_SEPARATOR;
        return $this;
    }
    
    /**
     * setLayoutParams
     *
     * @param array $params
     * @return $this
     */
    public function setLayoutParams($params = [])
    {
        $this->layoutParams = $params;
        return $this;
    }
    
    /**
     * getLayoutParams
     *
     * @return array
     */
    public function getLayoutParams()
    {
        return [];
    }

    /**
     * build
     *
     */
    public function build()
    {
        $content = '';
        foreach ($this->htmlParts as $part) {
            $content .= $this->getRenderedPart($part);
        }
        $this->setContent($content);
    }
    
    /**
     * getRenderedPart
     *
     * @param string $part
     * @return type
     */
    private function getRenderedPart($part)
    {
        return $this->setParams($this->getLayoutParams()[$part])
            ->setFilename($this->path . $part . self::LAYOUT_EXT)
            ->render()
            ->getContent();
    }

    /**
     * getHtmlParts
     *
     * @return type
     */
    protected function getHtmlParts()
    {
        return array_keys($this->getLayoutParams());
    }
}
