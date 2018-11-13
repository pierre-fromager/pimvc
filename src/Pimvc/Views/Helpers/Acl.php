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
        $this->content = '<div class="acl-manager">';
        $this->content .= '<h2>' . '<span class="fa fa-lock">&nbsp;</span>&nbsp;'
            . $this->title . '</h2>';
        foreach ($this->ressources as $controllerName => $actions) {
            $shortCrtl = $this->getClassnameFromNamespace($controllerName);
            $this->content .= '<div id="' . $shortCrtl . '" class="controler_header inactive">' . self::CR
                . '<h3 class="controler_header">' . $shortCrtl . '</h3>' . self::CR
                . '</div>' . self::CR // controler_header
                . '<div id="' . $shortCrtl . '_content" class="controler_content">' . self::CR;
            foreach ($actions as $actionName => $roles) {
                $this->content .= '<div id="' . $shortCrtl . '-' . $actionName . '" class="action_header inactive">' . self::CR
                    . '<h4 class="controler_header">' . $actionName . '</h4>' . self::CR
                    . '</div>' . self::CR // action_header
                    . '<div id="' . $shortCrtl . '-' . $actionName . '_content" class="action_content">' . self::CR;
                foreach ($roles as $roleName => $acl) {
                    $id = $shortCrtl . '-' . $actionName . '-' . $roleName;
                    $this->content .= '<div id="' . $id . '" class="role_header">' . self::CR
                        . '<a title="'.str_replace('-', ' ', $id).' : '.$acl.'" class="ajaxLink '.$acl.'" href="#" id="link' . $id . '">' . self::CR
                        . '<span class="role-acl">' . $roleName . '</span></a>' . self::CR
                        . '</div>' . self::CR // role_header
                        . '<div id="' . $id . '_content" class="role_content"></div>' . self::CR; // role_content
                }
                $this->content .= '</div>' . self::CR; // action_content
            }
            $this->content .= '</div>' . self::CR; // controler_content
        }
        $this->content .= '</div>';
        $this->content .= $this->getScript();
        return $this;
    }
    
    /**
     * getClassnameFromNamespace
     *
     * @param string $namespace
     * @return string
     */
    private function getClassnameFromNamespace($namespace)
    {
        return str_replace('\\', '_', $namespace);
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
     * getScript provides async acl features script
     *
     * @return string
     */
    protected function getScript()
    {
        $script = [];
        $script[] = '<script type="text/javascript">';
        $script[] = "var targetId = '';";
        $script[] = "var aclId = '';";
        $script[] = '$(document).ready(function() {';
        $script[] = '    $(".controler_header,.action_header").click(function() {';
        $script[] = '        $(this).toggleClass(\'active\');';
        $script[] = '        $(this).toggleClass(\'inactive\');';
        $script[] = '        targetId = $(this).attr(\'id\') + \'_content\';';
        $script[] = '        $(\'#\' + targetId).toggle();';
        $script[] = '    });';
        $script[] = '    $(".role_header").click(function() {';
        $script[] = '        aclId = $(this).attr(\'id\');';
        $script[] = '        $.get("' . $this->toggleUrl . '", { id : aclId },';
        $script[] = '        function(data){';
        $script[] = '            $(\'#link\' + aclId).removeClass(data.acl_disable);';
        $script[] = '            $(\'#link\' + aclId).addClass(data.acl_enable);';
        $script[] = '        });';
        $script[] = '    });';
        $script[] = '});';
        $script[] = '</script>';
        return implode(self::CR, $script);
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
