<?php

/**
 * Pimvc\Views\Helpers\Acl
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\Views\Helpers;

class Acl
{
    const ACL_ICONS_PATH = 'public/img/acl/';
    const CR = "\n";
    const MAX_ACL_ACTION = 4;
    const ACL_CONTROLLER_ACTION = 'acl/toggle';

    protected $content = '';
    protected $baseUrl = '';
    protected $toggleUrl = '';
    protected $ressources = array();
    protected $title = '';

    /**
     * @see __construct
     *
     * @param array $ressources
     */
    public function __construct($ressources)
    {
        $this->ressources = $ressources;
        $this->baseUrl = \Pimvc\App::getInstance()->getRequest()->getBaseUrl();
        $this->toggleUrl = $this->baseUrl . DIRECTORY_SEPARATOR . self::ACL_CONTROLLER_ACTION;
        $this->render();
        return $this;
    }

    /**
     * setTitle
     *
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * render
     *
     */
    public function render()
    {
        $view = (new \Pimvc\View())
            ->setFilename(__DIR__ . '/Template/Acl.php')
            ->setParams(
                [
                    'title' => $this->title,
                    'ressources' => $this->ressources,
                    'toggleUrl' => $this->toggleUrl
                ]
            )->render();
        $this->content = (string) $view;
        unset($view);
        return $this;
    }
    
    /**
     * change_key
     *
     * @param type $array
     * @param type $old_key
     * @param type $new_key
     * @return type
     */
    protected function changeArrayKeys($array, $old_key, $new_key)
    {
        if (!array_key_exists($old_key, $array)) {
            return $array;
        }
        $keys = array_keys($array);
        $keys[array_search($old_key, $keys)] = $new_key;
        return array_combine($keys, $array);
    }

    /**
     * @see __toString
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->content;
    }
}
