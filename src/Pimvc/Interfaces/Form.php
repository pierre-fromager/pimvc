<?php

/**
 * Description of Pimvc\Interfaces\Form
 *
 * @author pierrefromager
 */

namespace Pimvc\Interfaces;

interface Form
{
    const SUBMIT_LABEL = 'Valider';
    const CLEAN_LABEL = 'Effacer';
    const FORM_ELEMENT_ID_PREFIX = 'field_';
    const FORM_MANDATORY_FIELD_MENTIONS = '<small>(*) Champs obligatoires.</small>';
    const FORM_VALIDATOR_CLASS = '\\Pimvc\Input\Validators';
    const FORM_SIMPLE_SECTION_SIZE = 3;
    const FORM_DEFAULT_MODE = '';
    const FORM_FIELD_SEPARATOR = '-';
    const FORM_ICON_PATH = 'public/img/toolbar/';
    const FORM_FIELD_VALID_ICON = 'valid.png';
    const FORM_FIELD_VALID_ICON_MESSAGE = 'Valid';
    const FORM_FIELD_FAILED_ICON = 'warning.png';
    const FORM_XCSRF = 'xcsrf';
    const FORM_BREAK = '<br style="clear:both">';
    const PARAM_CLASS = 'class';
    const PARAM_ID = 'id';
    const PARAM_FIELD = 'Field';
    
    public function setFieldsExclude($fieldExlude);

    public function setName($name);

    public function setMethod($method);

    public function setDatas($datas);

    public function setRequest();

    public function setFields($fields);

    public function addButton($name, $label, $options = []);

    public function addSection($startField, $stopField, $param = []);

    public function getSection($fieldName, $mode = 'start');

    public function setOptions($options);

    public function disableLabel();

    public function getStyle();

    public function setWrapperClass($name, $class);

    public function setAlign($align);

    public function setEncType($enctype);

    public function get();

    public function render();

    public function getValidators();

    public function hasValidator($field);

    public function setValidator($field, $validator);

    public function setValidators($validators);

    public function unsetValidator($field);

    public function unsetValidators($validators);

    public function getErrors();

    public function setLabels($labels);

    public function setMode($mode = '');

    public function Setsectionsize($sectionSize = '');

    public function setType($fieldname, $value);

    public function setTypes($params);

    public function setValues($params);

    public function setValue($name, $value);

    public function setData($name, $value);

    public function setAction($name);

    public function isValid();

    public function __toString();

    public function setGroup($name, $values);

    public function setClass($name, $class);

    public function setExtra($field, $extras, $options = []);

    public function setExtras($extras);

    public function unsetExtra($field);

    public function setOperator($name, $value);

    public function getFields();

    public function setElementOptions($fieldName, $options);

    public function setElementsOptions($options);

    public function getElementOptions($fieldName);

    public function setEnableButtons($value);

    public function setSize($name, $value);

    public function setSizes($values);

    public function setExcludes($excludes);

    public function setFormClass($formClass);

    public function setFormId($formId);

    public function setFormTarget($target);

    public function setFormWrapperId($id);

    public function setEnableTranslate($enable);

    public function setValidLabelButton($label);

    public function setSearchMode($enable);

    public function getSearchWrapper($content);

    public function setEnableResetButton($enable);
}
