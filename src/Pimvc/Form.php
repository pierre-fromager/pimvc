<?php

/**
 * class Form
 * is a form generator
 * 
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc;

use Pimvc\Html\Element\Decorator;

class Form implements interfaces\form{
   
    protected $baseUrl = '';
    protected $fieldList = array();
    protected $fieldExlude = array();
    protected $errors = array();
    protected $validators = array();
    protected $data = array();
    protected $size = array();
    protected $name = null;
    protected $method = null;
    protected $action =null;
    protected $form = null;
    protected $encType = '';
    protected $groups = array();
    protected $classes = array();
    protected $request = null;
    protected $posted = array();
    protected $labels = array();
    protected $types = array();
    protected $mode = self::FORM_DEFAULT_MODE;
    protected $options = array();
    protected $width = 0;
    protected $sectionSize = self::FORM_SIMPLE_SECTION_SIZE;
    protected $fieldsAlign = 'left';
    protected $extras = array();
    protected $extras_class;
    protected $rootUrl;
    protected $elementsOptions = array();
    protected $enableButtons = true;
    protected $formClass = '';
    protected $formId = '';
    protected $formTarget = '';
    protected $formWrapperId = '';
    protected $enableTranslate = false;
    protected $operators = array();
    protected $disableLabel = false;
    protected $wrapperClasses = array();
    protected $validLabelButton;
    protected $enableResetButton;
    protected $isSearch = false;
    protected $buttons = array();
    protected $sections = array();
    protected $isPost = false;

    /**
     * __construct
     * 
     * @param array $fieldList
     * @param string $name
     * @param string $action
     * @param string $method
     * @param array $data
     */
    public function __construct($fieldList = [], $name = '', $action = '', $method = 'POST', $data = [], $fieldExlude = []) {
        $this->setFields($fieldList);
        $this->setFieldsExclude($fieldExlude);
        $this->setDatas($data);
        $this->setName($name);
        $this->setAction($action);
        $this->setMethod($method);
        $this->validLabelButton = self::SUBMIT_LABEL;
        $this->enableResetButton = false;
        return $this;
    }
    
    /**
     * setFieldsExclude
     * 
     * @param array $fieldExlude
     */
    public function setFieldsExclude($fieldExlude) {
        $this->fieldExlude = $fieldExlude;
        if ($this->fieldExlude){
            $this->fieldList = array_values(array_diff($this->fieldList, $fieldExlude));
        }
    }
    
    /**
     * setName
     * 
     * @param string $name
     * @return $this
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    
    /**
     * setMethod
     * 
     * @param string $method
     * @return $this
     */
    public function setMethod($method) {
        $this->method = $method;
        return $this;
    }
    
    /**
     * setDatas
     * 
     * @param array $datas
     * @return $this
     */
    public function setDatas($datas) {
        $this->data = $datas;
        return $this;
    }
    
    /**
     * setRequest
     * 
     * @param \Pimvc\Http\Request $request
     */
    public function setRequest(http\request $request) {
        $this->request = $request;
        $this->baseUrl = $this->request->getBaseUrl();
        $this->rootUrl = $this->request->getUrl();
        $this->isPost = ($this->request->getMethod() === 'POST');
        if ($this->isPost) {
            $this->posted = $request->get()[$request::REQUEST_P_REQUEST];
        }
        return $this;
    }
    
    /**
     * setFields
     * 
     * @param array $fields
     */
    public function setFields($fields) {
        if (!empty($fields) && !self::isAssoc($fields)) {
            $fields = array_map(
                array(__CLASS__, 'formatFieldsCallback')
                , $fields
            );
        }
        $this->fieldList = $fields;
        return $this;
    }

    /**
     * isAssoc returns true if $array is associativ
     * 
     * @param type $array
     * @return type 
     */
    private static function isAssoc($array) {   
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * formatFieldsCallback returns pre formated ready to use in form array
     * 
     * @param array $n
     * @return array 
     */
    private static function formatFieldsCallback($n) {
        return (array(self::PARAM_FIELD => $n));
    }
    
    /**
     * getButtons
     * 
     * @return string 
     */
    private function getButtons() {
        $buttons = '';
        foreach ($this->buttons as $name => $options) {
            $label = $options['label'];
            unset($options['label']);
            $buttons .= (string) new decorator(
                'button'
                , $label
                , $options
            );
        }
        return $buttons;
    }

    /**
     * addButton
     * 
     * @param string $name
     * @param array $options 
     */
    public function addButton($name, $label ,$options = array()) {
        $options[self::PARAM_ID] = (!isset($options[self::PARAM_ID])) 
            ? 'button_' . strtolower($name) 
            : $options[self::PARAM_ID];
        $options[self::PARAM_CLASS] = (!isset($options[self::PARAM_CLASS])) 
            ? 'btn btn-default' 
            : $options[self::PARAM_CLASS];
        $options['type'] = (!isset($options['type'])) 
            ? 'button' 
            : $options['type'];
        $options['label'] = (!isset($options['label'])) 
            ? $label 
            : $options['label'];
        $this->buttons[$name] = $options;
    }
    
    public function addSection($startField, $stopField, $param = array()) {
        $name = 'section_' . $startField . '___' . $stopField . '_';
        $this->sections[$name] = array(
            'name' => $name
            , 'start' => $startField
            , 'stop' => $stopField
            , 'params' => $param
        );
    }
    
    public function getSection($fieldName, $mode = 'start') {
        $section = '';
        $sections = array_filter(
            array_keys($this->sections)
            , function ($v) use ($fieldName){
                return strpos($v, '_' . $fieldName  . '_') !== false;
            }
        );
        if (sizeof($sections) == 1) {
            $sectionTag = 'div';
            $sectionNames = array_values($sections);
            $sectionName = $sectionNames[0];
            $sectionProps = $this->sections[$sectionName];
            $isStart = ($fieldName == $sectionProps['start']) && $mode == 'start';
            $isStop = ($fieldName == $sectionProps['stop']) && $mode == 'stop';
            if ($isStart || $isStop) {
                if ($isStart) {
                    $section = (string) new decorator(
                        $sectionTag
                        , ''
                        , $sectionProps['params']
                    );
                    $section = substr($section, 0, -6);
                } else {
                    $section = '</' . $sectionTag . '>';
                }
            }
        }
        return $section;
    }

        /**
     * setOptions
     * 
     * @param array $options 
     */
    public function setOptions($options) {
        $this->options = $options;
    }
    
    /**
     * disableLabel
     * 
     */
    public function disableLabel(){
        $this->disableLabel = true;
    }

        /**
     * getStyle
     * 
     * @return string 
     */
    public function getStyle() {
        $width = (!empty($this->width)) ? 'width:' . $this->width . ';' : '';
        $style = 'style="' . $width . '"';
        return $style;
    }

    /**
     * checkbox
     * 
     * @param type $label
     * @param type $name
     * @param type $datas
     * @param type $checkedvalues
     * @return type 
     */
    protected function checkbox($label, $name, $datas, $checkedvalues) {
        $requiredSign = ($this->isRequired($name) && empty($this->mode) && !$this->disableLabel) 
            ? ' (*)' 
            : '';
        $legendLabel = (isset($this->labels[$name])) ? $this->labels[$name] : $label;
        $checkboxes = '<fieldset class="radiogroup ' . $this->fieldsAlign . '">'
            . '<legend class="radiogroup">'
            . '<span class="radiogroup">' . $legendLabel . $requiredSign . '</span>'
            . '</legend>'
            . '<ul class="radiogroup">';
        $error = $this->getError($name);
        $label = $this->getLabel($name, $label);
        $type = 'checkbox';
        
        foreach ($datas as $key => $value) {
            $checked = ((is_array($checkedvalues)) && (in_array($key, $checkedvalues)))
                ? 'checked="checked"' 
                : '';        
            $id = 'field_' . $name . '_' . $key;
            $checkboxes .= '<li class="radiogroup ' . $this->fieldsAlign . '">'
                . '<label class="radiogroup">' ;
            $checkboxes .= $this->getInput(
                $id
                , 'form ' . $this->fieldsAlign
                , $name . '[]'
                , $type
                , $key
                , $this->mode
                , $error
                , $checked
            );
            $checkboxes .= $value . '</label></li>';
        }
        $checkboxes .= '</ul></fieldset>';
        return $this->getWrapper($name, $checkboxes);
    }
    
    /**
     * getWrapper
     * 
     * @param string $name
     * @param string $content
     * @return string 
     */
    private function getWrapper($name, $content) {
        $wrapperClass = (isset($this->wrapperClasses[$name])) 
            ? $this->wrapperClasses[$name]
            : 'form-element-wrapper ' . $this->fieldsAlign;
        $wrapperOptions = array(
            'id' => 'element-wrapper-' . $name
            , 'class' => $wrapperClass
        );
        return (string) new decorator(
            'div'
            , $content
            , $wrapperOptions
        );      
    }
    
    /**
     * setWrapperClass
     * 
     * @param string $name
     * @param string $class 
     */
    public function setWrapperClass($name, $class) {
        $this->wrapperClasses[$name] = $class;
    }

    /**
     * radio
     * 
     * @param type $label
     * @param type $name
     * @param type $datas
     * @param type $checkedvalues
     * @return type 
     */
    protected function radio($label, $name, $datas, $checkedvalues) {
        $requiredSign = ($this->isRequired($name) && empty($this->mode) && !$this->disableLabel) 
            ? ' (*)' 
            : '';
        if (!$this->mode == 'readonly') {
            $checkboxes = '<fieldset class="radiogroup">'
                    . '<legend class="radiogroup">'
                    . '<span>' . ucfirst($label) . $requiredSign . '</span>'
                    . '</legend>'
                    . '<ul class="radiogroup">';
            $hasCheckValue = (!empty($checkedvalues));
            $error = $this->getError($name);
            $label = $this->getLabel($name, $label);
            $type = 'radio';
            foreach ($datas as $key => $value) {
                $checked = '';
                if ($hasCheckValue) {
                    $checked = ($key == $checkedvalues) 
                        ? 'checked="checked"' 
                        : '';
                }
                $id = 'field_' . $name . '_' . $key;
                $checkboxes .= '<li class="radiogroup ' 
                        . $this->fieldsAlign . '">'
                        . '<label class="radiogroup">';
                $checkboxes .= $this->getInput(
                    $id
                    , 'form ' . $this->fieldsAlign
                    , $name
                    , $type
                    , $key
                    , $mode = ''
                    , $error
                    , $checked
                );
                $checkboxes .= $value . '</label></li>';
            }
            $checkboxes .= '</ul></fieldset>';
            $checkboxes = '<div class="form-element-wrapper col-sm-6">' 
                . $checkboxes . '</div>';
        } else {
            $dataValue = (isset($datas[$checkedvalues])) 
                ? $datas[$checkedvalues] 
                : '';
            $id = 'field_' . $name;
            $checkboxes = $this->input(
                $label
                , 'text'
                , $name
                , $dataValue
            );
        }
        return $checkboxes;
    }

    /**
     * Entrée select de formulaire
     * 
     * @param string $label
     * @param string $type
     * @param string $name
     * @param string $value
     */
    protected function select($label, $name, $datas, $selectedvalue, $class = '') {
        $options = '';
        $selectTitle = ($class) ? $class : '- Selectionner -';
        if (!$class) {
            $datas = array('' => $selectTitle) + $datas;
        }     
        $requiredSign = ($this->isRequired($name) && empty($this->mode) && !$this->disableLabel) 
            ? ' (*)'
            : '';
        $error = $this->getError($name);
        $label = (empty($label)) ? $label : $this->getLabel($name, $label);
        $selectOptionsRender = $this->renderElementOptions($name);
        $isReadOnly = ($this->mode == 'readonly="readonly"');
        if (!$isReadOnly) {
            foreach ($datas as $key => $value) {
                $selected = ($key == $selectedvalue) 
                    ? 'selected="selected"' 
                    : '';
                $options.= '<option value="' . $key . '" ' . $selected . '>' 
                    . $value . '</option>';
            }
            $select = '<select'
                .' title="' . $name . '"'
                .' class="select form-control '.$class.'"'
                .' name="' . $name . '"'
                .' id="field_' . $name . '"'
                . $selectOptionsRender
                .'>'
                . $options 
                . '</select>' 
                . $error;
        } else {
            $realValue = (isset($datas[$selectedvalue])) 
                ? $datas[$selectedvalue] 
                : '';
            $select = $this->getInput(
                'field_' . $name
                , 'form'
                , 'field_' . $name
                , 'text'
                , $realValue
                , $this->mode
                , $error
            );
        }
        $wrapper = (empty($label)) ? '':'<p class="form ' . $this->fieldsAlign . '">' 
            . ucfirst($label) . $requiredSign . '</p>';
        $select = $wrapper . $select;
        return (empty($label)) 
            ? $select 
            : $this->getWrapper($name, $select);
    }

    /**
     * Entrée select de formulaire
     * 
     * @param string $label
     * @param string $type
     * @param string $name
     * @param string $value
     */
    protected function mselect($label, $name, $datas, $selectedvalue, $class = '') {
        $options = '';
        /*
        $selectTitle = ($class) ? $class : '- Selectionner -';
        if (!$class) {
            $datas = array('' => $selectTitle) + $datas;
        }*/
        $requiredSign = ($this->isRequired($name) 
            && empty($this->mode) 
            && !$this->disableLabel
        ) 
            ? ' (*)' 
            : '';
        $error = $this->getError($name);
        $label = (empty($label)) ? $label : $this->getLabel($name, $label);
        $selectOptionsRender = $this->renderElementOptions($name);
        if (is_array($datas)) {
            foreach ($datas as $key => $value) {
                    $isSelected = (is_array($selectedvalue)) && (in_array($key, $selectedvalue));
                    $selected = ($isSelected) ? 'selected="selected"' : '';
                    $options.= '<option value="' . $key . '" ' . $selected . '>'
                        . $value . '</option>';               
            }
        }
        $select = '<select'
            . ' '.($this->mode == 'readonly="readonly"' ? 'disabled="disabled"' : "").' '
            . ' title="' . $name . '"'
            . ' class="form chosen' . $class . '"' //multiple
            . ' multiple '
            . ' name="' . $name . '[]"'
            . ' id="field_' . $name . '"'
            . $selectOptionsRender
            . '>'
            . $options
            . '</select>'
            . $error;

        $wrapper = (empty($label)) 
            ? '' 
            : '<p class="form ' . $this->fieldsAlign . '">'
                . ucfirst($label) . $requiredSign . '</p>'
                . '<br style="clear:both"/>';
        $select = $wrapper . $select;
        return (empty($label)) 
            ? $select 
            : $this->elementWrapper(
                $select
                , array('class' => 'form-element-wrapper col-sm-6')
            );
    }

    /**
     * Entrée input de formulaire
     * 
     * @param string $label
     * @param string $type
     * @param string $name
     * @param string $value
     */
    protected function input($label, $type, $name, $value, $options = array()) {
        $input = '';
        $class = (isset($this->classes[$name])) 
            ? $this->classes[$name] 
            : 'form';
        $id = self::FORM_ELEMENT_ID_PREFIX . $name;
        if (!empty($options)) {
            $class .= (isset($options[self::PARAM_CLASS])) 
                ? ' ' . $options[self::PARAM_CLASS] 
                : '';
            $id = (isset($options['id'])) 
                ? $options['id'] 
                : $id;
        }
        $error = $this->getError($name);
        $label = ($type != 'hidden') 
            ? $this->getFormatedLabel(
                $name
                , $this->getLabel($name, $label)
            ) 
            : '';
        $isAjax = ($type == 'autocomplete');
        $input = ($isAjax) 
            ? $this->getInputAjax(
                    $id . '_ajax'
                    , $class
                    , ''
                    , $value
                    , ''
                )
                . $this->getInput(
                    $id
                    , $class
                    , $name
                    , 'hidden'
                    , $value
                    , $this->mode
                    , $error
                ) 
            : $this->getInput(
                    $id
                    , $class
                    , $name
                    , $type
                    , $value
                    , $this->mode
                    , $error
        );
        $operator = ($this->hasOperator($name)) 
            ? $this->getOperatorElement($name, $this->getOperator($name)) 
            : '';
        return $this->getWrapper($name, $label . $operator . $input);
    }
    
    /**
     * getFormatedLabel
     * 
     * @param string $name
     * @param string $labelName
     * @param string $requiredSign
     * @return string 
     */
    private function getFormatedLabel($name, $labelName) {
        $requiredSign = $this->getRequiredSign($name);
        return (string) new decorator(
            'p'
            , $labelName . $requiredSign
            , array(
                'id' => 'label_' . $name
                , 'class' => 'form ' . $this->fieldsAlign
            )
        );
    }
    
    /**
     * getRequiredSign
     * 
     * @param string $name
     * @return string 
     */
    private function getRequiredSign($name) {
        return ($this->isRequired($name) 
                && empty($this->mode) 
                && !$this->disableLabel
            ) ? ' (*)' : '';
    }

    /**
     * elementWrapper
     * 
     * @param string $content
     * @param array $options
     * @return string 
     */
    protected function elementWrapper($content = '', $options = array()) {
        $class = (isset($options['class'])) 
            ? $options['class'] 
            : 'form-element-wrapper ';
        return (string) new decorator(
            'div'
            , $content
            , array('class' => $class.' ' . $this->fieldsAlign)
        );
    }
    
    /**
     * setAlign
     * 
     * @param string $align 
     */
    public function setAlign($align) {
        $this->fieldsAlign = $align;
    }

    /**
     * getInputAjax
     * 
     * @param string $id
     * @param string $class
     * @param string $value
     * @return string 
     */
    private function getInputAjax($id, $class, $value) {
        $baseUrl = $this->baseUrl;
        $input = new decorator(
            'input'
            , ''
            , array(
                'id' => $id
                , 'class' => $class
                , 'type' => 'text'
                , 'value' => $value
            )
        );
        $img = new decorator(
            'img'
            , ''
            , array(
                'id' => $id . '_delete'
                , 'class' => 'form'
                , 'src' => $baseUrl . 'public/images/toolbar/delete.png'
                , 'value' => $value
            )
        );
        $inputAjax = (string) $input . (string) $img;
        unset($input);
        unset($img);
        return $inputAjax;
    }
    
    /**
     * getInput
     * 
     * @param string $id
     * @param string $class
     * @param string $name
     * @param string $type
     * @param string $value
     * @param string $mode
     * @param string $error
     * @return string 
     */
    private function getInput($id, $class, $name, $type, $value, $mode, $error, $checked = '') {
        $steps = '';
        if ($type == 'file' || $name == 'filename') {
            $value = '';
        }
        if ($type == 'number') {
            $steps = 'step="any"';
        }
        if ($type == 'checkbox' && ! empty($mode) &&$mode != 'normal' && $mode != 'search') {
            $mode = 'disabled="disabled"';
        } else {
            $mode = $this->mode;
        }
        if (is_array($value)) {
            $value = '';
        }
        $defaultInputOptions = array(
            'id' => $id
            , 'class' => $class
            , 'name' => $name
            , 'size' => (isset($this->size[$name])) ? $this->size[$name] : ''
            , 'maxlength' => (isset($this->size[$name])) ? $this->size[$name] : ''
            , 'value' => $value
            , 'type' => $type
        );
        if (strpos($this->mode, 'readonly') !== false) {
            $defaultInputOptions['readonly'] = 'readonly';
        }
        if (strpos($checked, 'checked') !== false) {
            $defaultInputOptions['checked'] = 'checked';
        }
        return (string) new decorator(
            'input'
            , ''
            , array_merge($defaultInputOptions, $this->getElementOptions($name))
        ) . $error;
        /*
        return '<input id="' . $id . '" class="' . $class
            . '" type="' . $type . '" name="' . $name . '" ' . $size
            . ' value="' . $value . '" ' . $steps . ' '
            . $checked . ' '
            . $mode . $optionsRender
            . '/>' . $error;*/
    }

    /**
     * Entrée texte de formulaire
     * 
     * @param string $label
     * @param string $type
     * @param string $name
     * @param string $value
     */
    protected function textarea($label, $type, $name, $value) {
        $label = $this->getFormatedLabel($name, $this->getLabel($name, $label));
        $isReadonly = strpos($this->mode, 'readonly') !== false;
        $textareaDefaultOptions = array(
            'class' => 'form'
            , 'name' => $name
        );
        if ($isReadonly) {
            $textareaDefaultOptions['class'] = 'form ' . $this->fieldsAlign 
                . ' textarea col-sm-12';            
            $textarea = (string) new decorator(
                'div'
                , $value
                , array_merge(
                    $textareaDefaultOptions
                    , $this->getElementOptions($name)
                )
            );
        } else {
            $textarea = (string) new decorator(
                'textarea'
                , $value
                , array_merge(
                    $textareaDefaultOptions
                    , $this->getElementOptions($name)
                )
            );
        }
        $bypass = (empty($value) && $isReadonly);
        return (!$bypass) 
            ? $this->getWrapper(
                $name 
                , $label . $textarea . $this->getError($name)
            ) 
            : '';
    }

    /**
     * Boutton de soumission du formulaire
     * 
     * @param string $label
     */
    protected function submit($label) {
        $submitOptions = array(
            'id' => 'submit-' . $this->name
            , 'class' => 'btn btn-success btn-right form-submit'
            , 'type' => 'submit'
            , 'value' => $label
                
        );
        $submit = (string) new decorator(
            'input'
            , ''
            , $submitOptions
        );
        return ($this->mode !=  'readonly') ? $submit : '';
    }
    
    /**
     * Boutton de nettoyage du formulaire
     * 
     * @param string $label
     */
    protected function clean($label) {
        $clean = '<input class="btn btn-default form-reset right" type="reset" value="' . $label . '"/>';
        return ($this->mode != 'readonly') ? $clean : '';
    }

    /**
     * Boutton de reset du wizzard formulaire
     * 
     * @param string $label
     */
    protected function resetWizzard($label) {
        $onclick = "window.location.replace(document.URL + '/reset/true')";
        $wizzard = '<input class="form-reset btn btn-default right" type="button"'
            . ' onclick="' . $onclick . '" value="' . $label . '"/>';
        return ($this->mode != 'readonly') ? $wizzard : '';
    }

    /**
     * getEncType
     * 
     * @return string 
     */
    private function getEncType() {
        return $this->encType;
    }
    
    /**
     * setEncType
     * 
     * @param string $enctype
     * @return string 
     */
    public function setEncType($enctype) {
        return $this->encType = $enctype;
    }

    /**
     * get is form fabric
     * 
     */
    public function get() {
        $form = null;
        $input = null;
        $options = array();
        $counterSection = 0;
        $formId = (!empty($this->formId)) ? $this->formId : 'form' . ucfirst($this->name);
        $maxSection = $this->sectionSize;
        $countField = count($this->fieldList);
        $needSection = ($countField > $maxSection);
        $advancedId = "'" . '#advanced-' . $formId . "'";
        $sectionStart ="";
//$sectionStart = '<br style="clear:both">' . PHP_EOL
//            . '<div'
//            . ' title="Advanced criterias"'
//            . ' class="form_inactive"'
//            . ' onclick="$j(' . $advancedId . ').toggle();$j(this).toggleClass(\'form_active\')"'
//            . '>&nbsp;</div>' . PHP_EOL . '<div id="advanced-' . $formId . '" style="display:none">';
        $sectionStop = ""; //PHP_EOL . '</div>';
        foreach ($this->fieldList as $aField) {
            $fieldName = $aField[self::PARAM_FIELD];
            $input .= $this->getSection($fieldName,'start');
            if (!in_array($fieldName, $this->fieldExlude)) {
                ++$counterSection;
                $input .= ($needSection && $counterSection === $maxSection) 
                    ? $sectionStart 
                    : '';
                $options[self::PARAM_CLASS] = '';
                switch ($fieldName) {
                    case 'id':
                    case self::FORM_XCSRF:
                        $typeElement = 'hidden';
                        break;
                    case 'password':
                        $typeElement = 'password';
                        break;
                    case 'email':
                        $typeElement = 'text';
                        $options[self::PARAM_CLASS] = 'email';
                        break;
                    case 'content':
                    /*case 'message':
                        $typeElement = 'textarea';
                        break;*/
                    case 'code_utilisation_support':
                    case 'code_type_support':
                        $typeElement = 'list';
                        break;
                    //case 'datec':
                    //case 'datet':
                    //case 'date':
                    case 'weekDay':
                    //case 'datem':
                    case 'days':
                        $typeElement = 'text';
                        $options[self::PARAM_CLASS] = (empty($this->mode)) 
                            ? 'date-picker' 
                            : '';
                        break;
                    case 'hours':
                    case 'minute':
                        $typeElement = 'text';
                        $options[self::PARAM_CLASS] = 'time-picker';
                        break;
                    default:
                        $typeElement = 'text';
                        break;
                }

                // @TODO : Hydrate separate method
                $data = (isset($this->data[$aField[self::PARAM_FIELD]])) 
                    ? $this->data[$aField[self::PARAM_FIELD]] 
                    : '';
                $dataPosted = (isset($this->posted[$aField[self::PARAM_FIELD]])) 
                    ? $this->posted[$aField[self::PARAM_FIELD]] 
                    : $data;
                $typeElement = (isset($this->types[$aField[self::PARAM_FIELD]])) 
                    ? $this->types[$aField[self::PARAM_FIELD]] 
                    : $typeElement;
                if (!is_array($data)) {
                    $input.= ($typeElement != 'textarea') 
                        ? $this->input(
                            $aField[self::PARAM_FIELD]
                            , $typeElement
                            , $aField[self::PARAM_FIELD]
                            , $dataPosted
                            , $options
                         ) 
                        : $this->textarea(
                            $aField[self::PARAM_FIELD]
                            , $typeElement
                            , $aField[self::PARAM_FIELD]
                            , $dataPosted
                         );
                } else {
                    $isCheckbox = isset($this->types[$aField[self::PARAM_FIELD]])
                        && ($this->types[$aField[self::PARAM_FIELD]]) == 'checkbox';
                    $isRadio = isset($this->types[$aField[self::PARAM_FIELD]])
                        && ($this->types[$aField[self::PARAM_FIELD]]) == 'radio';
                    $isMselect = isset($this->types[$aField[self::PARAM_FIELD]])
                        && ($this->types[$aField[self::PARAM_FIELD]]) == 'mselect';
                    if ($isCheckbox) {
                        $input.= $this->checkbox(
                            $aField[self::PARAM_FIELD]
                            , $aField[self::PARAM_FIELD]
                            , $data
                            , $dataPosted
                        );
                    } else if ($isRadio) {
                        $input.= $this->radio(
                            $aField[self::PARAM_FIELD]
                            , $aField[self::PARAM_FIELD]
                            , $data
                            , $dataPosted
                        );
                    } else if ($isMselect) {
                        $input.= $this->mselect(
                            $aField[self::PARAM_FIELD]
                            , $aField[self::PARAM_FIELD]
                            , $data
                            , $dataPosted
                        );
                    } else {
                        $input.= $this->select(
                            $aField[self::PARAM_FIELD]
                            , $aField[self::PARAM_FIELD]
                            , $data
                            , $dataPosted
                        );
                    }
                }
                $input .= ($needSection && ($countField === $counterSection)) 
                    ? $sectionStop 
                    : '';
                $extra = ($this->hasExtra($aField[self::PARAM_FIELD])) 
                    ? $this->getExtra($aField[self::PARAM_FIELD]) 
                    : '';
                $input .= $extra;
            }
            $input .= $this->getSection($fieldName,'stop');
        }
        $fillBreaker = ($this->mode != 'readonly') ? self::FORM_BREAK : '';
        $mandatoryMention = (empty($this->mode) && !$this->disableLabel) 
            ? $fillBreaker . self::FORM_MANDATORY_FIELD_MENTIONS 
            : '';
        $buttons =  '<div class="form-element-wrapper col-sm-12">' 
            . $this->getButtons() 
            . '</div>';
        $buttons .= ($this->enableButtons) 
            ? $fillBreaker . $this->submit($this->validLabelButton)
                . $this->clean(self::CLEAN_LABEL) 
            : '';
        $buttons .= ($this->enableResetButton) 
            ? $this->resetWizzard('Réinitialiser les filtres')               
            : '';
        $buttons .= $fillBreaker;
        $formClasses = ($this->formClass) ? $this->formClass : 'form';    
        $defaultOptions = array(
            self::PARAM_CLASS => $formClasses
            , 'id' => $formId
            , 'name' => $this->name
            , 'method' => $this->method
            , 'action' => $this->action
            , 'target' => $this->formTarget
            , 'enctype' => $this->getEncType()
        );
        $formOptions = ($this->options) 
            ? array_merge($defaultOptions, $this->options) 
            : $defaultOptions;
        $formDecorator = new decorator(
            'form'
            ,  $input . $buttons . $mandatoryMention
            , $formOptions
        );
        $formDecorator->render();
        $form = (string) $formDecorator;
        unset($formDecorator);
        $wrappedForm = ($this->formWrapperId) 
            ? $this->getFormWrapper($form) 
            : $form;      
        return $this->getSearchWrapper($wrappedForm);
    }
    
    /**
     * render
     * 
     */
    public function render() {
        $this->form = $this->get();
    }

    /**
     * isRequired returns true if a validator is setted
     * 
     * @param string $name
     * @return boolean 
     */
    protected function isRequired($name) {
        return (isset($this->validators[$name]));
    }
    
    /**
     * getValidators
     * 
     * @return array 
     */
    public function getValidators() {
        return $this->validators;
    }
    
    /**
     * getValidators
     * 
     * @return array 
     */
    public function hasValidator($field) {
        return isset($this->validators[$field]);
    }
    /**
     * setValidator
     * 
     * @param string $field
     * @param string $validator 
     */
    public function setValidator($field, $validator) {
        $this->validators[$field] = $validator;
    }
    
    /**
     * setValidators
     * 
     * @param array $validators 
     */
    public function setValidators($validators) {
        foreach ($validators as $name => $validator) {
            $this->setValidator($name, $validator);
        }
    }
    
    /**
     * unsetValidator
     * 
     * @param string $field 
     */
    public function unsetValidator($field){
        unset($this->validators[$field]);
    }
    
    /**
     * unsetValidators
     * 
     * @param array $validators 
     */
    public function unsetValidators($validators) {
        foreach ($validators as $name => $validator) {
            $this->unsetValidator($name);
        }
    }
    
    /**
     * setError
     * 
     * @param string $field
     * @param string $message 
     */
    protected function setError($field, $message) {
        $this->errors[$field] =  $message ;
    }

    
    /**
     * setErrors
     * 
     * @param array $errors
     */
    protected function setErrors($errors) {
        foreach ($errors as $name => $error) {
            $this->setError($name, $error);
        }
    }
    
    /**
     * getValidIcon
     * 
     * @param string $error
     * @return string 
     */
    private function getValidIcon($name) {
        $icon = '';
        $error = (isset($this->errors[$name])) ? $this->errors[$name] : '';
        $linkError = $this->getLinkError($name);
        if ($this->isPost) {
            $icon = (!empty($error)) 
                ? $linkError . $this->getIcon(self::FORM_FIELD_FAILED_ICON, $error)
                : $this->getIcon(
                    self::FORM_FIELD_VALID_ICON
                    , self::FORM_FIELD_VALID_ICON_MESSAGE
                );
        } else {
            $icon = '';
        }
        return $icon;
    }
    
    /**
     * getLinkError
     * 
     * @param string $name
     * @return string 
     */
    private function getLinkError($name) {
        return '<a class="linkError" name="error_' . $name . '"></a>';
    }
    
    /**
     * getIcon
     * 
     * @return string 
     */
    private function getIcon($filename, $title) {
        $iconPath = $this->rootUrl . self::FORM_ICON_PATH;
        return '<img'
            . ' title="' . $title . '"'
            . ' alt="' . $title . '"'
            . ' style="margin-left:10px"'
            . ' src="' . $iconPath . $filename . '"'
            . '/>';
    }

    /**
     * getError
     * 
     * @param string $name
     * @return string 
     */
    protected function getError($name) {
        $isError = (isset($this->errors[$name]));
        $message = '';
        $isXcsrf = ($name == self::FORM_XCSRF);
        $checkedIcon = (!$isXcsrf) ? $this->getValidIcon('') : '';
        if ($this->hasValidator($name)) {
            $message = ($isError) 
                ? $this->getValidIcon($name) 
                : $checkedIcon;
        }
        return $message;
    }
    
    /**
     * getErrors
     * 
     * @return array 
     */
    public function getErrors() {
        return $this->errors;
    }


    /**
     * setLabel
     * 
     * @param string $name
     * @param string $label 
     */
    protected function setLabel($name, $label) {
        $this->labels[$name] = $label;
        return $this;
    }
    
    /**
     * setLabels
     * 
     * @param array $params 
     */
    public function setLabels($labels) {
        $this->labels = $labels;
        return $this;
    }      
 
    /**
     * setMode
     * 
     * @param string $mode 
     */
    public function setMode($mode = '') {
        switch ($mode) {
            case 'readonly': $this->mode = 'readonly="readonly"';
                break;
            case 'hidden': $this->mode = 'style="hidden"';
                break;
            case 'disabled': $this->mode = 'disabled="disabled"';
                break;
            default:$this->mode = $mode;
                break;
        }
    }

    /**
     * Setsectionsize
     * 
     * @param int $sectionSize 
     */
    public function Setsectionsize($sectionSize = '') {
        $this->sectionSize = $sectionSize;    
    } 
 
    /**
     * setType
     * 
     * @param string $fieldname
     * @param string $value 
     */
    public function setType($fieldname, $value) {
        $this->types[$fieldname] = $value;
    }
    
    /**
     * setTypes
     * 
     * @param array $params 
     */
    public function setTypes($params) {
        foreach ($params as $fieldname => $value) {
            $this->setType($fieldname, $value);
        }      
    }
    /**
     * getLabel
     * 
     * @param string $name
     * @return string 
     */
    protected function getLabel($name, $label) {
        $label = (isset($this->labels[$name])) 
            ? $this->labels[$name] 
            : $label;
        return (!$this->disableLabel) ? $label : '';
        }
    
    /**
     * setValues
     * 
     * @param array $params 
     */
    public function setValues($params) {
        foreach ($params as $name => $value) {
            $this->setValue($name, $value);
        }
    }
    
    /**
     * setValue
     * 
     * @param string $name
     * @param string $value 
     */
    public function setValue($name, $value) {
        $this->posted[$name] = $value;
    }
    
    
    /**
     * setData
     * 
     * @param string $name
     * @param string $value 
     */
    public function setData($name, $value) {
        $this->data[$name] = $value;
    }
    
    /**
     * setAction
     * 
     * @param string $name
     * @param string $value 
     */
    public function setAction($name) {
        $this->action = $name;
        return $this;
    }
    

    /**
     * isValid
     * 
     */
    public function isValid() {
        foreach ($this->validators as $key => $value) {
            $validatorParams = array();
            $hasValidatorData = isset($this->posted[$key]);
            $validate = false;
            if ($hasValidatorData) {
                $validatorParams[] = $this->posted[$key];
                if (strpos($value, self::FORM_FIELD_SEPARATOR) !== false) {
                    $expl = explode(self::FORM_FIELD_SEPARATOR, $value);
                    $value = $expl[0];
                    $initialValue = $value;
                    $validatorParams[] = $expl[1];
                }
                $callBack = array(self::FORM_VALIDATOR_CLASS, $value);
                $initialValue = $validatorParams[0];
                $initialLength = (is_array($initialValue)) 
                    ? count($initialValue) 
                    : strlen($initialValue);
                $validate = call_user_func_array(
                    $callBack
                    , $validatorParams
                );
            }
            if (!$validate) {
                $errorMessagePrefix = 'Saisir ';
                $validatorName = strtolower($value);
                switch ($validatorName) {
                    case 'isshortdate': 
                        $errorMessage =  $errorMessagePrefix .'une date valide.';
                        break;                   
                    case 'isrequired': 
                        $errorMessage =  $errorMessagePrefix .'une valeur.';
                        break;
                    case 'isnumeric': 
                        $errorMessage =  $errorMessagePrefix .'un nombre.';
                        break;
                    case 'isnumericnotrequired': 
                        $errorMessage =  $errorMessagePrefix .'un nombre.';
                        break;
                    case 'isboolnotrequired': 
                        $errorMessage =  $errorMessagePrefix .'un booléen.';
                        break;
                    case 'isbool': 
                        $errorMessage =  $errorMessagePrefix .'un booléen.';
                        break;
                    case 'isemail': 
                        $errorMessage = $errorMessagePrefix . 'une adresse email.';
                        break;
                    case 'isminlen': 
                        $errorMessage = $errorMessagePrefix . 'taille &gt; ' . $expl[1]. ', ici ' . $initialLength;
                        break;                   
                    case 'ismaxlen': 
                        $errorMessage = $errorMessagePrefix . 'taille &lt; ' . $expl[1]. ', ici ' . $initialLength;
                        break;
                    case 'islenbetween': 
                        $errorMessage = $errorMessagePrefix . 'longueur entre ' 
                            . str_replace('_', ' et ', $expl[1]) . ', ici ' . $initialLength;
                        break;
                     case 'ispassword': 
                        $errorMessage = $errorMessagePrefix . 'combinaison alpha numeric et longueur entre ' 
                            . str_replace('_', ' et ', $expl[1]) ;
                        break;
                     case 'validxcsrf': 
                        $errorMessage = 'Cross-site request forgery attempt.';
                        break;
                    default : $errorMessage =  'Erreur validateur ' . $validatorName .'.';
                }
                $this->setError($key,$errorMessage);
            }
        }
        $isValid = (count($this->errors) == 0);
        $this->render();
        return $isValid;
    }
    

    /**
     * Affiche du formulaire
     */
    public function __toString() {
        return $this->form;
    }
    
    /**
     * setGroup
     * 
     * @param string $name
     * @param array $values 
     */
    public function setGroup($name, $values) {
        if (is_array($values)) {
            $this->groups[$name] = $values;
        }
    }
    
    /**
     * getGroup
     * 
     * @param string $name
     * @return array 
     */
    private function getGroup($name) {
        return $this->groups[$name];
    }
    
    /**
     * setClass
     * 
     * @param string $name
     * @param string $class 
     */
    public function setClass($name, $class) {
        $this->classes[$name] = $class;
    }
    
    /**
     * setExtra
     * 
     * @param string $after
     * @param string $extras 
     */
    public function setExtra($field, $extras, $options = array()) {
        $this->extras[$field] = $extras;
        if (isset($options['class'])) {
            $this->extras_class[$field] = $options['class'];
        }
        
    }
    
    /**
     * getExtraClasses
     * 
     * @param string $field
     * @return string 
     */
    private function getExtraClasses($field) {
        return (isset($this->extras_class[$field])) ? $this->extras_class : '';
    }


    /**
     * setExtras
     * 
     * @param array $extras
     */
    public function setExtras($extras) {
        foreach($extras as $k => $v) {
            $this->setExtra($k, $v);
        }
    }
    
    /**
     * unsetExtra
     * 
     * @param string $field 
     */
    public function unsetExtra($field) {
        if ($this->hasExtra($field)) {
            unset($this->extras[$field]);
        } 
    }
    
    /**
     * getExtra
     * 
     * @param string $field
     * @return string 
     */
    private function getExtra($field, $options = array()) {
        $extra = '';
        if ($this->hasExtra($field)) {
            $extra = $this->extras[$field];
            if (isset($this->extras_class[$field])) {
                $options = array('class' => $this->extras_class[$field]);
                $extra = (string) new decorator(
                    'div'
                    , $extra
                    , $options
                );
            }
        }
        return $extra;
    }
    
    /**
     * hasExtra
     * 
     * @param string $field
     * @return boolean 
     */
    private function hasExtra($field) {
        return isset($this->extras[$field]);
    }
    
    /**
     * setOperator
     * 
     * @param string $name
     * @param string $type 
     */
    public function setOperator($name, $value) {
        $this->operators[$name] = $value;
    }
    
    /**
     * getOperator
     * 
     * @param string $name
     * @return string || false
     */
    private function getOperator($name) {
        return $this->hasOperator($name) ? $this->operators[$name] : false;
    }


    /**
     * setOperator
     * 
     * @param string $name
     * @param string $type 
     */
    private function hasOperator($name) {
        return isset($this->operators[$name]); 
    }
    
    /**
     * getOperatorElement
     * 
     * @param type $name
     * @return type 
     */
    private function getOperatorElement($name, $value) {
        $operatorType = array('=', '!=', 'in', '!in','>','<');
        $operatorLabel = array('=', '&ne;', '&sub;', '&nsub;', '&gt;','&lt;');
        $operatorData = array_combine($operatorType, $operatorLabel);
        return $this->select('', 'operator[' . $name . ']', $operatorData, $value, 'op left');
    }

    /**
     * getFields
     * 
     * @return array 
     */
    public function getFields() {
        return $this->getFieldsname($this->fieldList);
    }
    
    /**
     * getFieldsname
     * 
     * @param array $fieldList
     * @return array 
     */
    private function getFieldsname($fieldList) {
        $callBack = array($this, 'getFieldname');
        return array_map($callBack, $fieldList);
    }
    
    /**
     * getFieldname
     * 
     * @param array $field
     * @return string 
     */
    private static function getFieldname($field) {
        return $field[self::PARAM_FIELD];
    }
    
    /**
     * setElementOptions
     * 
     * @param string $fieldName
     * @param array $options 
     */
    public function setElementOptions($fieldName, $options) {
        $this->elementsOptions[$fieldName] = $options;
    }
    
    /**
     * setElementsOptions
     * 
     * @param string $fieldName
     * @param array $options 
     */
    public function setElementsOptions($options) {
        $this->elementsOptions = $options;
    }
    
    /**
     * getElementOptions
     * 
     * @param string $fieldName
     * @return array 
     */
    public function getElementOptions($fieldName) {
        return ($this->hasElementOptions($fieldName)) 
            ? $this->elementsOptions[$fieldName] 
            : array();
    }
    
    /**
     * hasElementOptions
     * 
     * @param string $fieldName
     * @return boolean 
     */
    private function hasElementOptions($fieldName) {
        return isset($this->elementsOptions[$fieldName]);
    }
    
    /**
     * renderElementOptions
     * 
     * @param string $fieldName
     * @return string 
     */
    private function renderElementOptions($fieldName) {
        $render = '';
        $options = $this->getElementOptions($fieldName);
        foreach ($options as $key => $value) {
            $render.= ' ' . $key . '="' . $value . '"';
        }
        unset($options);
        return $render;
    }
    
    /**
     * setEnableButtons
     * 
     * @param boolean $value 
     */
    public function setEnableButtons($value) {
        $this->enableButtons = $value;
    }
    
    /**
     * setSize
     * 
     * @param string $name
     * @param string $value 
     */
    public function setSize($name, $value) {
        $this->size[$name] = $value;
    }
    
    /**
     * setSizes
     * 
     * @param array $values
     * @param string $value 
     */
    public function setSizes($values) {
        foreach ($values as $k => $v) {
            $this->setSize($k, $v);
        }
    }
    
    /**
     * setExcludes
     * 
     * @param array $excludes 
     */
    public function setExcludes($excludes) {
        $this->fieldExlude = $excludes;      
    }
    
    /**
     * setFormClass
     * 
     * @param string $formClass 
     */
    public function setFormClass($formClass) {
        $this->formClass = $formClass;
    }
    
    /**
     * setFormId
     * 
     * @param string $formId 
     */
    public function setFormId($formId) {
        $this->formId = $formId;
    }
    
    /**
     * setFormTarget
     * 
     * @param string $target 
     */
    public function setFormTarget($target) {
        $this->formTarget = $target;
    }

    /**
     * setFormWrapperId
     * 
     * @param string $id 
     */
    public function setFormWrapperId($id) {
        $this->formWrapperId = $id;   
    }
    
    /**
     * setEnableTranslate
     * 
     * @param boolean $enable 
     */
    public function setEnableTranslate($enable) {
        $this->enableTranslate = $enable;
    }

    /**
     * setValidLabelButton
     * 
     * @param string $label 
     */
    public function setValidLabelButton($label) {
        $this->validLabelButton = $label;
    }
    
    /**
     * setSearchMode
     * 
     * @param boolean $enable 
     */
    public function setSearchMode($enable) {
        $this->isSearch = $enable;
    }
    
    /**
     * getSearchWrapper
     * 
     * @param type $content
     * @return type 
     */
    public function getSearchWrapper($content) {
        $advancedId = 'advanced-' . $this->formId;
        $script = '$j(\'#' . $advancedId . '\').toggle();'
            . '$j(this).toggleClass(\'form_active\');'
            . '$j(\'#iconCriteria\').toggleClass(\'glyphicon-chevron-down\',\'glyphicon-chevron-up\')';
        $wrapper = (!$this->isSearch) 
            ? $content 
            : '<div id="criteriaFilterWrapper" class="row-fluid">' 
            . '<span id="criteriaFilter" onclick="' . $script . '">'
            . 'Critères'                       
            . '<i id="iconCriteria" class="glyphicon glyphicon-chevron-down"> </i>'
            . '</span>' . PHP_EOL
            . '<div id="' . $advancedId . '" style="display:none">'
            . $content
            . '</div>'
            . '</div>';
        return $wrapper;
    }

    /**
     * setEnableResetButton
     * 
     * @param boolean $enable 
     */
    public function setEnableResetButton($enable) {
        $this->enableResetButton = $enable;
    }

    /**
     * getFormWrapper
     * 
     * @param type $param 
     */
    private function getFormWrapper($content) {
        return '<div id="' . $this->formWrapperId . '">' . $content . '</div>';
    }

}