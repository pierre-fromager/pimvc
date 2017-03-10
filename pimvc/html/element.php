<?php

/**
 * Description of Lib_Html_Element
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace lib\html;

class element

{
    const mainMenuId = 'menuDeroulant';
    const mainMenuStyle ='background:transparent;';
    const itemMenuClass = 'large_menu corner';
    const docTypeContentTransitional = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    const docTypeContentStrict ='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
    const docTypeContentHtml5 = '<!DOCTYPE html>';
    const docTypeTransitional = 'transitional';
    const docTypeStrict = 'strict';
    const docTypeHtml5 = 'html5';
    const classOption = ' class="';
    const titleOption = ' title="';
    const idOption = ' id="';

    protected static $docType;

    private static function tag($tagName,$content){
        return '<'.$tagName.'>'.$content.'</'.$tagName.'>'."\n";
    }

    public function setDocType($type){
        $docType ='';
        switch ($type) {
            case self::docTypeTransitional : $this->docType = self::docTypeContentTransitional;break;
            case self::docTypeHtml5 : self::$docType = self::docTypeContentHtml5;break;
            case self::docTypeStrict : $this->docType = self::docTypeContentStrict;break;
        }
    }

    public static function clean($content)
    {
        $content = str_replace('>', ">\n",$content);
        $content = str_replace('/>', "/>\n",$content);
        $content = str_replace('</', "\n</",$content);
        return $content;
    }

    public static function getBreadcrum($action)
    {
        if (!empty($action)) {
            $arr=explode('/', $action);
            $className = $arr[0];
            $methodName = $arr[1];
            return '<h4>'.Translate_translate::get(Tools::labelMaker($className)) .' &gt;&gt; '
            .Translate_translate::get(Tools::labelMaker($methodName)).'</h4>';
        }
        return false;
    }

    public static function html($type,$content)
    {
        self::setDocType($type);
        return self::$docType.self::tag(__FUNCTION__, $content);
    }

    public static function header($content)
    {
        return self::tag(__FUNCTION__, $content);
    }

    public static function nav($content)
    {
        return self::tag(__FUNCTION__, $content);
    }

    public static function section($content)
    {
        return self::tag(__FUNCTION__, $content);
    }

    public static function article($content)
    {
        return self::tag(__FUNCTION__, $content);
    }

    public static function aside($content)
    {
        return self::tag(__FUNCTION__, $content);
    }

    public static function footer($content)
    {
        return self::tag(__FUNCTION__, $content);
    }
    public static function head($content)
    {
        return self::tag(__FUNCTION__, $content);
    }

    public static function title($content)
    {
        return self::tag(__FUNCTION__, $content);
    }

    public static function body($content)
    {
        return self::tag(__FUNCTION__, $content);
    }

    public static function script($content)
    {
        return self::tag(__FUNCTION__, $content);
    }

    public static function style($content)
    {
        return self::tag(__FUNCTION__, $content);
    }

    public static function link($param,$option = array())
    {
        $id = '';
        $class = '';
        $title = '';
        if (!empty($option)) {
            $class = (isset($option['class'])) ? self::classOption.$option['class'].'" ' : '';
            $title = (isset($option['title'])) ? self::titleOption.$option['title'].'" ' : '';
            $id = (isset($option['id'])) ? self::idOption. $option['id'] .'" ' : '';
        }
        return '<a '.$title.$id.$class.'href="'.$param['link'].'">'.$param['value'].'</a>';
    }

    public static function subItemMenu($param)
    {
        return '<li class="'.self::itemMenuClass.'">'
        .self::link(array(
            'link'=>$param['link'],
            'value'=>$param['title'],
        ))
        .'</li>';
    }

    public static function itemMenu($param)
    {
        return '<li class="'.self::itemMenuClass.'">'
        .self::link(array(
                    'link'=>'#',
                    'value'=>$param['title'],
        ))
        .'<ul class="sousMenu">'
        .$param['content']
        .'</ul>'
        .'</li>';
    }

    public static function mainMenu($param)
    {
        return '<ul id="'.self::mainMenuId.'" style="'.self::mainMenuStyle.'">'.$param['content'].'</ul>';
    }
}