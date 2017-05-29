<?php

/**
 * Helper_Acl
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\Views\Helpers;

use Pimvc\Views\Helpers\Bootstrap\Button as bootstrapButton;

class Acl {

    const ACL_ICONS_PATH = 'public/img/acl/';
    const CR = "\n";
    const MAX_ACL_ACTION = 4;
    const ACL_TITLE = 'Gestion des droits.';
    const ACL_CONTROLLER_ACTION = 'acl/toggle';

    protected $content = '';
    protected $baseUrl = '';
    protected $toggleUrl = '';
    protected $ressources = array();

    /**
     * @see __construct
     * 
     * @param array $ressources 
     */
    public function __construct($ressources) {
        $this->ressources = $ressources;
        $this->baseUrl = \Pimvc\App::getInstance()->getRequest()->getBaseUrl();
        $this->toggleUrl = $this->baseUrl . DIRECTORY_SEPARATOR . self::ACL_CONTROLLER_ACTION;
        $this->process();
    }

    /**
     * process
     * 
     */
    protected function process() {
        $cr = "\n";
        $this->content = '<div class="acl-manager">';
        $applyButton = new bootstrapButton('Appliquer');
        $applyButton->setType(bootstrapButton::TYPE_SUCCESS);
        $applyButton->setSize(bootstrapButton::SIZE_SMALL);
        $applyButton->setStyle('float:right');
        $applyButton->setDatalink($this->baseUrl . 'acl/list');
        $applyButton->render();
        $this->content .= '<h2>'.self::ACL_TITLE.(string) $applyButton.'</h2>';
        unset($applyButton);
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
                        . '<div id="' . $id . '_content" class="role_content">' . self::CR
                    . '</div>' . self::CR; // role_content                 
                }
                $this->content .= '</div>' . self::CR; // action_content  
            }
            $this->content .= '</div>' . self::CR; // controler_content
        }
        $this->content .= '</div>';
        $this->content .= $this->getScript();
    }
    
    /**
     * getClassnameFromNamespace
     * 
     * @param string $namespace
     * @return string
     */
    private function getClassnameFromNamespace($namespace) {
        return substr($namespace, 1 + strrpos($namespace,'\\'), strlen($namespace));
    }

    /**
     * change_key
     * 
     * @param type $array
     * @param type $old_key
     * @param type $new_key
     * @return type
     */
    protected function changeArrayKeys($array, $old_key, $new_key) {

        if (!array_key_exists($old_key, $array))
            return $array;

        $keys = array_keys($array);
        $keys[array_search($old_key, $keys)] = $new_key;

        return array_combine($keys, $array);
    }

    /**
     * getScript provides async acl features script
     * 
     * @return string 
     */
    protected function getScript() {
        $script = '<script type="text/javascript">' . self::CR;
        $script .= "var targetId = '';" . self::CR;
        $script .= "var aclId = '';" . self::CR;
        $script .= '$(document).ready(function() {' . self::CR;
        $script .= '    $(".controler_header,.action_header").click(function() {' . self::CR;
        $script .= '        $(this).toggleClass(\'active\');' . self::CR;
        $script .= '        $(this).toggleClass(\'inactive\');' . self::CR;
        $script .= '        targetId = $(this).attr(\'id\') + \'_content\';' . self::CR;
        $script .= '        $(\'#\' + targetId).toggle();' . self::CR;
        $script .= '    });' . self::CR;
        $script .= '    $(".role_header").click(function() {' . self::CR;
        $script .= '        aclId = $(this).attr(\'id\');' . self::CR;
        $script .= '        $.get("' . $this->toggleUrl . '", { id : aclId },' . self::CR;
        $script .= '        function(data){' . self::CR;
        $script .= '            $(\'#link\' + aclId).removeClass(data.acl_disable);' . self::CR;
        $script .= '            $(\'#link\' + aclId).addClass(data.acl_enable);' . self::CR;
        $script .= '        });' . self::CR;
        $script .= '    });' . self::CR;
        $script .= '});' . self::CR;
        $script .= '</script>' . self::CR;
        return $script;
    }

    /**
     * @see __toString
     * 
     * @return string 
     */
    public function __toString() {
        return (string) $this->content;
    }

}

