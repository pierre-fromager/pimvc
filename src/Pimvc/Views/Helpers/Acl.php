<?php

/**
 * Helper_Acl
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\Views\Helpers;

use Pimvc\Views\Helpers\Bootstrap\Button as bootstrapButton;

class Acl {

    const ACL_ICONS_PATH = 'public/images/acl/';
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
            $this->content .= '<div id="' . $controllerName . '" class="controler_header inactive">' . self::CR;
            $this->content .= '<h3 class="controler_header">' . $controllerName . '</h3>' . self::CR;
            $this->content .= '</div>' . self::CR; // controler_header
            $this->content .= '<div id="' . $controllerName . '_content" class="controler_content">';
            foreach ($actions as $actionName => $roles) {
                $this->content .= '<div id="' . $controllerName . '-' . $actionName . '" class="action_header inactive">' . self::CR;
                $this->content .= '<h4 class="controler_header">' . $actionName . '</h4>' . self::CR;
                $this->content .= '</div>' . self::CR; // action_header
                $this->content .= '<div id="' . $controllerName . '-' . $actionName . '_content" class="action_content">' . self::CR;
                foreach ($roles as $roleName => $acl) {
                    $id = $controllerName . '-' . $actionName . '-' . $roleName;
                    $this->content .= '<div id="' . $id . '" class="role_header">' . self::CR;
                    $this->content .= '<a title="'.str_replace('-', ' ', $id).' : '.$acl.'" class="ajaxLink '.$acl.'" href="#" id="link' . $id . '">';
                    $this->content .= '<span class="role-acl">' . $roleName . '</span></a>' . self::CR;
                    $this->content .= '</div>' . self::CR; // role_header
                    $this->content .= '<div id="' . $id . '_content" class="role_content">' . self::CR;
                    $this->content .= '</div>' . self::CR; // role_content                 
                }
                $this->content .= '</div>' . self::CR; // action_content  
            }
            $this->content .= '</div>' . self::CR; // controler_content
        }
        $this->content .= '</div>';
        $this->content .= $this->getScript();
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
        $script .= '$j(document).ready(function() {' . self::CR;
        $script .= '    $j(".controler_header,.action_header").click(function() {' . self::CR;
        $script .= '        $j(this).toggleClass(\'active\');' . self::CR;
        $script .= '        $j(this).toggleClass(\'inactive\');' . self::CR;
        $script .= '        targetId = $j(this).attr(\'id\') + \'_content\';' . self::CR;
        $script .= '        $j(\'#\' + targetId).toggle();' . self::CR;
        $script .= '    });' . self::CR;
        $script .= '    $j(".role_header").click(function() {' . self::CR;
        $script .= '        aclId = $j(this).attr(\'id\');' . self::CR;
        $script .= '        $j.get("' . $this->toggleUrl . '", { id : aclId },' . self::CR;
        $script .= '        function(data){' . self::CR;
        $script .= '            $j(\'#link\' + aclId).removeClass(data.acl_disable);' . self::CR;
        $script .= '            $j(\'#link\' + aclId).addClass(data.acl_enable);' . self::CR;
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

