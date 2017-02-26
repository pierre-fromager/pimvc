<?php

/**
 * Filtering and validating for forms, input parameters, etc
 *
 * @author Julien Ricard
 * @tester Pierre Fromager
 * @category Security
 * @package Pimvc
 */

namespace lib;

class input {

    const F_INTEGER = 'Int';
    const F_NATURAL = 'Natural';
    const F_NATURALNONZERO = 'NaturalNonZero';
    const F_ALPHA = 'Alpha';
    const F_ALPHANUM = 'AlphaNum';
    const F_NUMERIC = 'Numeric';
    const F_BASE64 = 'Base64';
    const F_REGEXP = 'Regexp';
    const F_STRING = 'String';
    const F_TRIM = 'Trim';
    const F_URL = 'Url';
    const F_STRIPTAGS = 'StripTags';
    const F_NULL = 'NullIfEmptyString';
    const F_BOOLEAN = 'Boolean';
    const V_INTEGER = 'Int';
    const V_NATURAL = 'Natural';
    const V_NATURALNONZERO = 'NaturalNonZero';
    const V_ALPHA = 'Alpha';
    const V_ALPHANUM = 'AlphaNum';
    const V_NUMERIC = 'Numeric';
    const V_BASE64 = 'Base64';
    const V_EQUALS = 'Equals';
    const V_REGEXP = 'Regexp';
    const V_REQUIRED = 'Required';
    const V_NOTEMPTY = 'NotEmpty';
    const V_GREATERTHAN = 'GreaterThan';
    const V_LESSTHAN = 'LessThan';
    const V_MINLENGTH = 'MinLength';
    const V_MAXLENGTH = 'MaxLength';
    const V_EXACTLENGTH = 'ExactLength';
    const V_EMAIL = 'Email';
    const V_MATCHES = 'Matches';
    const V_URL = 'Url';
    const V_DEFAULT = 'Default';
    const V_BOOLEAN = 'Boolean';
    const PARAM_FILTER = 'filter';
    const PARAM_VALIDATE = 'validate';
    const PARAM_VALUE = 'value';

    /**
     * An array of every input parameter
     */
    private $_params = array();

    /**
     * An array of every filter instantiated for every input parameter
     */
    private $_filters = array();

    /**
     * An array of every validator instantiated for every input parameter
     */
    private $_validators = array();

    /**
     * An array of every parameter after filtering and validating
     */
    private $_fields = array();

    const REGEXP_ALPHA = '/[^a-z]*/i';
    const REGEXP_ALPHANUM = '/[^a-z0-9]*/i';
    const REGEXP_BASE64 = '%[^a-zA-Z0-9/+=]*%i';
    const REGEXP_INT = '/^[\-+]?[0-9]+$/';

    /**
     * The constructor to use while filtering/validating input
     *
     * @param array $params the input paramters to filter and validate
     * @param array $filters the list of filters for each parameter
     * @param array $validators the list of validators for each parameter
     *
     * @return Input
     */
    public function __construct(array $params, array $filters, array $validators) {
        $this->_params = $params;
        $this->_filters = $filters;
        $this->_validators = $validators;
        $this->_classMethods = get_class_methods(__CLASS__);
        $refl = new \ReflectionClass(__CLASS__);
        $this->_classConstants = $refl->getConstants();
    }

    /**
     * __destruct
     * 
     */
    public function __destruct() {
        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }

    /**
     * This method has to be called after specifying the parameters, filters and validators
     *
     * @param void
     *
     * @return bool returns true if every validator is validating, or false if any validator is not
     * This method always filters before validating
     */
    public function isValid() {
        // 1) filter
        $this->filter();
        // 2) validate
        return $this->validate();
    }

    /**
     * Filters every input parameter
     *
     * @return void
     */
    public function filter() {
        $this->metaFilterAndValidate(self::PARAM_FILTER);
    }

    /**
     * Validates every input parameter
     *
     * @return void
     */
    public function validate() {
        return $this->metaFilterAndValidate(self::PARAM_VALIDATE);
    }

    /**
     * Returns every incorrect field and the corresponding validator
     *
     * @return array
     */
    public function getMessages() {
        $messages = array();
        foreach ($this->_fields as $fieldName => $values) {
            if (!$values['isValid']) {
                $invalidators = array();
                foreach ($values['validators'] as $validatorName => $validatorValue) {
                    if (!$validatorValue) {
                        $invalidators[] = $validatorName;
                    }
                }
                $messages[$fieldName] = $invalidators;
                unset($validator);
            }
            unset($fieldName);
            unset($values);
        }
        return $messages;
    }

    /**
     * Returns the original input parameters
     *
     * @return array
     */
    public function getFields() {
        return $this->_fields;
    }

    /**
     * Returns every input parameter after content filtering
     *
     * @return array
     */
    public function getFilteredFields() {
        $filteredFields = array();
        foreach ($this->_fields as $fieldName => $data) {
            $filteredFields[$fieldName] = $data[self::PARAM_VALUE];
        }
        return $filteredFields;
    }

    /**
     * Does all the work needed to filter and validate the input parameters
     *
     * @param string $metaAction ("filter" or "validate")
     * @return mixed
     */
    private function metaFilterAndValidate($metaAction) {
        if ($metaAction == self::PARAM_FILTER) {
            $metaSource = $this->_filters;
        } elseif ($metaAction == self::PARAM_VALIDATE) {
            $metaSource = $this->_validators;
            $isValid = true;
        }
        foreach ($metaSource as $paramName => $options) {
            if ($metaAction == self::PARAM_FILTER) {
                $this->setField(
                        $paramName
                        , (isset($this->_params[$paramName]) ? $this->_params[$paramName] : null
                        )
                );
            }
            if ($metaAction == self::PARAM_VALIDATE) {
                if (!isset($this->_fields[$paramName])) {
                    $this->setField(
                            $paramName
                            , (isset($this->_params[$paramName]) ? $this->_params[$paramName] : null
                            )
                    );
                }
                $validators = array();
            }
            $options = (array) $options;
            foreach ($options as $option) {
                // optional parameter sent to the filter/validator
                // by default, it's not set
                unset($optionParameter);
                if (is_array($option)) {
                    $optionKeys = array_keys($option);
                    $optionValues = array_values($option);
                    // call with an alias and a parameter: array('isValidId' => '\App\Toto::validateId', 22)
                    if (isset($option[0]) && $optionKeys[1] == 0) {
                        $optionName = $optionKeys[0];
                        $optionFunction = $optionValues[0];
                        $optionParameter = $optionValues[1];
                    } elseif ($this->isAssoc($option)) {
                        // call with an alias only : array('isValidId' => '\App\Toto::validateId'),
                        // or (if your name is Olivier D) call with the parameter as assoc : array('default' => 7),
                        $optionKeys = array_keys($option);
                        $optionValues = array_values($option);
                        // if the value of the array is a function
                        if (isset($$optionFunction)) {
                            $optionName = $optionKeys[0];
                            $optionFunction = $optionValues[0];
                        } else {
                            // if the value of the array is a function (à la Olivier D)
                            $optionName = $optionKeys[0];
                            $optionFunction = $optionKeys[0];
                            $optionParameter = $optionValues[0];
                        }
                    } else {
                        // call with a parameter only : array('regexp', '/[a-z]*/i')
                        $optionName = $option[0];
                        $optionFunction = $option[0];
                        $optionParameter = $option[1];
                    }
                } else {
                    $optionName = $option;
                    $optionFunction = $option;
                }
                // if we want to validate against a method of a model
                $idx = strpos($optionFunction, '::');
                if ($idx !== false) {
                    // find (with autoload) the class and call the method
                    $className = substr($optionFunction, 0, $idx);
                    $methodName = substr($optionFunction, $idx + 2);
                    if ($metaAction == self::PARAM_FILTER) {
                        if (isset($optionParameter)) {
                            $this->setField(
                                    $paramName
                                    , $className::$methodName(
                                            $this->_fields[$paramName][self::PARAM_VALUE]
                                            , $optionParameter
                                    )
                            );
                        } else {
                            $this->setField(
                                    $paramName
                                    , $className::$methodName(
                                            $this->_fields[$paramName][self::PARAM_VALUE]
                                    )
                            );
                        }
                    } elseif ($metaAction == self::PARAM_VALIDATE) {
                        if (isset($optionParameter)) {
                            $ret = $className::$methodName(
                                            $this->_fields[$paramName][self::PARAM_VALUE]
                                            , $optionParameter
                                            , $this
                            );
                        } else {
                            $ret = $className::$methodName(
                                            $this->_fields[$paramName][self::PARAM_VALUE]
                                            , null
                                            , $this
                            );
                        }
                        // add the validator to the validators for this field
                        $isValid = $isValid && $ret;
                        $validators[$optionName] = $ret;
                    }
                } else {
                    // we will search for the function name in this class
                    $methodNameForOption = $metaAction . ucfirst($optionFunction);
                    // if the developer has used a shortname for the filter/validator
                    $hasFilter = ($metaAction == self::PARAM_FILTER);
                    $methodNameFromConstants = ($hasFilter) ? 'F' : 'V';
                    $methodNameFromConstants .= '_' . strtoupper($optionFunction);
                    if (isset($this->_classConstants[$methodNameFromConstants])) {
                        $methodNameForOption = ($hasFilter) ? self::PARAM_FILTER : self::PARAM_VALIDATE;
                        $methodNameForOption .= $this->_classConstants[$methodNameFromConstants];
                    }

                    if (in_array($methodNameForOption, $this->_classMethods)) {
                        if ($methodNameForOption == 'validateRequired') {
                            $ret = array_key_exists($paramName, $this->_params);
                        } else {
                            if (!isset($optionParameter)) {
                                $optionParameter = null;
                            }
                            if (is_array($this->_fields[$paramName][self::PARAM_VALUE])) {
                                if ($metaAction == self::PARAM_FILTER) {
                                    foreach ($this->_fields[$paramName][self::PARAM_VALUE] as $paramKey => $paramValue) {
                                        $this->_fields[$paramName][self::PARAM_VALUE][$paramKey] = self::$methodNameForOption($this->_fields[$paramName][self::PARAM_VALUE][$paramKey], $optionParameter, $this);
                                    }
                                    unset($paramKey);
                                    unset($paramValue);
                                    $ret = $this->_fields[$paramName][self::PARAM_VALUE];
                                } else {
                                    $ret = true;
                                    foreach ($this->_fields[$paramName][self::PARAM_VALUE] as $paramKey => $paramValue) {
                                        $ret &= self::$methodNameForOption($this->_fields[$paramName][self::PARAM_VALUE][$paramKey], $optionParameter, $this);
                                    }
                                    unset($paramKey);
                                    unset($paramValue);
                                }
                            } else {
                                $ret = self::$methodNameForOption($this->_fields[$paramName][self::PARAM_VALUE], $optionParameter, $this);
                            }
                        }
                        if ($metaAction == self::PARAM_FILTER) {
                            $this->setField($paramName, $ret);
                        }
                        // add the validator to the validators for this field
                        if ($metaAction == self::PARAM_VALIDATE) {
                            // special case of the default value
                            if ($methodNameForOption == 'validateDefault') {
                                if (is_array($this->_fields[$paramName][self::PARAM_VALUE])) {
                                    foreach ($this->_fields[$paramName][self::PARAM_VALUE] as $paramKey => $paramValue) {
                                        if (empty($this->_fields[$paramName][self::PARAM_VALUE][$paramKey])) {
                                            $this->_fields[$paramName][self::PARAM_VALUE][$paramKey] = $optionParameter;
                                        }
                                    }
                                    unset($paramKey);
                                    unset($paramValue);
                                    $ret = true;
                                } else {
                                    if (empty($this->_fields[$paramName][self::PARAM_VALUE])) {
                                        $this->_fields[$paramName][self::PARAM_VALUE] = $optionParameter;
                                    }
                                    $ret = true;
                                }
                            }
                            $isValid = $isValid && $ret;
                            $validators[$optionName] = $ret;
                        }
                    } else {
                        throw new \Exception(
                        __CLASS__ . ' hasn\'t a method called "'
                        . $methodNameForOption . '"'
                        );
                    }
                }
            }
            unset($option);

            // we set the field after all the input value went through all validators
            if ($metaAction == self::PARAM_VALIDATE) {
                // we test for each params if one of validators is not valid.
                $paramIsValid = true;
                foreach ($validators as $v) {
                    if ($v === false) {
                        $paramIsValid = false;
                        break;
                    }
                }
                $this->setField($paramName, false, $paramIsValid, $validators);
            }
        }
        if ($metaAction == self::PARAM_VALIDATE) {
            return $isValid;
        }
    }

    /**
     * After filtering or validating, updates the field with additional data
     *
     * @param mixed $paramName the name of the input parameter
     * @param mixed $value the value after filtering
     * @param boolean $isValid is the field valid
     * @param array $validators sets the given validators for this parameter
     *
     * @return mixed
     */
    private function setField($paramName, $value = false, $isValid = null, $validators = null) {
        if (!isset($this->_fields[$paramName])) {
            $this->_fields[$paramName] = array(
                'originalValue' => (isset($this->_params[$paramName])) ? $this->_params[$paramName] : null
                , self::PARAM_VALUE => (isset($this->_params[$paramName])) ? $this->_params[$paramName] : null
                , 'isValid' => true
                , 'validators' => array()
            );
        }
        if ($value !== false) {
            $this->_fields[$paramName][self::PARAM_VALUE] = $value;
        }
        if ($isValid !== null) {
            $this->_fields[$paramName]['isValid'] = $this->_fields[$paramName]['isValid'] && $isValid;
        }
        if ($validators !== null) {
            $this->_fields[$paramName]['validators'] = $validators;
        }
    }

    /**
     * Returns the filtered value for any field given in the params
     *
     * @param mixed $paramName the name of the input parameter
     *
     * @return mixed
     */
    public function __get($paramName) {
        return $this->_fields[$paramName][self::PARAM_VALUE];
    }

    /**
     * Returns true or false if the input parameter was specified within the instanciation
     *
     * @param mixed $paramName the name of the input parameter
     *
     * @return boolean
     */
    public function __isset($paramName) {
        return isset($this->_fields[$paramName][self::PARAM_VALUE]);
    }

    /**
     * Indicates if the array is an associative one or not
     *
     * @param array $paramName the name of the input parameter
     *
     * @return boolean
     */
    private function isAssoc($array) {
        return (
            is_array($array) && array_diff_key(
                $array
                , array_keys(array_keys($array))
            )
        );
    }

    // ************************************************************************
    // filter functions
    // ************************************************************************

    /**
     * Used for filtering integer as string in json data
     *
     * @param mixed $value the value of the input parameter
     *
     * @return mixed
     */
    public static function filterNullIfEmptyString($value) {
        if ($value == '') return null;
        return $value;
    }

    /**
     * Parses the value as an integer
     *
     * @param mixed $value the value of the input parameter
     * @return mixed
     */
    public static function filterInt($value) {
        return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Parses the value as a natural (positive integer)
     *
     * @param mixed $value the value of the input parameter
     * @return mixed
     */
    public static function filterNatural($value) {
        return abs(self::filterInt($value));
    }

    /**
     * Parses the value as a strict natural (strictly positive integer)
     *
     * @param mixed $value the value of the input parameter
     * @return mixed
     */
    public static function filterNaturalNonZero($value) {
        $natural = self::filterNatural($value);
        if ($natural != 0) {
            return $natural;
        } else {
            return null;
        }
    }

    /**
     * Parses the value as alpha (letters only, no digit)
     *
     * @param mixed $value the value of the input parameter
     * @return mixed
     */
    public static function filterAlpha($value) {
        return preg_replace(self::REGEXP_ALPHA, '', $value);
    }

    /**
     * Parses the value as an alphanumeric
     *
     * @param mixed $value the value of the input parameter
     * @return mixed
     */
    public static function filterAlphaNum($value) {
        return preg_replace(self::REGEXP_ALPHANUM, '', $value);
    }

    /**
     * Parses the value as a base64 string
     *
     * @param mixed $value the value of the input parameter
     * @return mixed
     */
    public static function filterBase64($value) {
        return preg_replace(self::REGEXP_BASE64, '', $value);
    }

    /**
     * Parses the value as a boolean
     *
     * @param mixed $value the value of the input parameter
     * @return mixed
     */
    public static function filterBoolean($value) {
        $out = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        return $out;
    }

    /**
     * Parses the value as a float
     *
     * @param mixed $value the value of the input parameter
     * @return mixed
     */
    public static function filterNumeric($value) {
        return filter_var(
                $value
                , FILTER_SANITIZE_NUMBER_FLOAT
                , FILTER_FLAG_ALLOW_FRACTION
        );
    }

    /**
     * Removes the html tags from input
     *
     * @param mixed $value the value of the input parameter
     * @return mixed
     */
    public static function filterStripTags($value) {
        return strip_tags($value);
    }

    /**
     * Parses the value along a regexp
     *
     * @param mixed $value the value of the input parameter
     * @param string $regexp the regular expression to filter the input parameter to

     * @return mixed
     */
    public static function filterRegexp($value, $regexp) {
        return preg_replace($regexp, '', $value);
    }

    /**
     * Parses the value as a string
     *
     * @param mixed $value the value of the input parameter
     * @return mixed
     */
    public static function filterString($value) {
        return filter_var(
                $value
                , FILTER_SANITIZE_STRING
                , FILTER_FLAG_STRIP_LOW
        );
    }

    /**
     * Trims the string (default php behaviour)
     *
     * @param mixed $value the value of the input parameter
     * @return mixed
     */
    public static function filterTrim($value) {
        return trim($value);
    }

    /**
     * Filters the input string along the php's internal regexp for a url
     *
     * @param mixed $value the value of the input parameter
     * @return mixed
     */
    public static function filterUrl($value) {
        return filter_var($value, FILTER_SANITIZE_URL);
    }

    // ************************************************************************
    // validator functions
    // ************************************************************************

    /**
     * Validates that the input value is an integer
     *
     * @param mixed $value the value of the input parameter
     * @return mixed
     */
    public static function validateInt($value) {
        return (self::filterInt($value) == $value);
    }

    /**
     * Validates that the input value is a natural
     *
     * @param mixed $value the value of the input parameter
     * @return mixed
     */
    public static function validateNatural($value) {
        return (self::filterNatural($value) == $value);
    }

    /**
     * Validates that the input value is a positive integer greater than zero
     *
     * @param mixed $value the value of the input parameter
     * @return mixed
     */
    public static function validateNaturalNonZero($value) {
        return (self::filterNaturalNonZero($value) == $value);
    }

    /**
     * Validates that the input value is alpha (letters only)
     *
     * @param mixed $value the value of the input parameter
     * @return mixed
     */
    public static function validateAlpha($value) {
        return (bool) preg_match(self::REGEXP_ALPHA, $value);
    }

    /**
     * Validates that the input value is an alphanumeric string
     *
     * @param mixed $value the value of the input parameter
     * @return mixed
     */
    public static function validateAlphaNum($value) {
        return (bool) preg_match(self::REGEXP_ALPHANUM, $value);
    }

    /**
     * Validates that the input value is a base64 string
     *
     * @param mixed $value the value of the input parameter
     * @return mixed
     */
    public static function validateBase64($value) {
        return (bool) preg_match(self::REGEXP_BASE64, $value);
    }

    /**
     * Validates that the input value is a boolean
     *
     * @param mixed $value the value of the input parameter
     * @return mixed
     */
    public static function validateBoolean($value) {
        return (self::filterBoolean($value) == $value);
    }

    /**
     * Validates that the input value is a number (float)
     *
     * @param mixed $value the value of the input parameter
     * @return mixed
     */
    public static function validateNumeric($value, $compare, $instance) {
        return (self::filterNumeric($value) == $value);
    }

    /**
     * Validates that the input value equals the second parameter (no type checking)
     *
     * @param mixed $value the value of the input parameter
     * @param mixed $check the value of the second parameter
     *
     * @return mixed
     */
    public static function validateEquals($value, $check) {
        return (bool) ($value == $check);
    }

    /**
     * Validates that the input value along a regular expression given as second parameter
     *
     * @param mixed $value the value of the input parameter
     * @param string $regexp the regular expression to validate to
     * @return mixed
     */
    public static function validateRegexp($value, $regexp) {
        return (bool) preg_match($regexp, $value);
    }

    /**
     * This method actually does not exist :) and should not be called directly
     *
     * @param mixed $value the value of the input parameter
     * @return mixed
     */
    public static function validateRequired($value) {
        throw new \Exception('This method should never be called');
    }

    /**
     * Validates that the input value is not an empty string
     *
     * @param mixed $value the value of the input parameter
     * @return mixed
     */
    public static function validateNotEmpty($value) {
        return !(trim($value) === '');
    }

    /**
     * Validates that the input value is greater than the given parameter
     *
     * @param mixed $value the value of the input parameter
     * @param mixed $compare the value to compare to
     * @return mixed
     */
    public static function validateGreaterThan($value, $compare) {
        return ($value >= $compare);
    }

    /**
     * Validates that the input value is lesser than the given parameter
     *
     * @param mixed $value the value of the input parameter
     * @param mixed $compare the value to compare to
     * @return mixed
     */
    public static function validateLessThan($value, $compare) {
        return ($value <= $compare);
    }

    /**
     * Validates that the input value has a minimum length of the given parameter
     *
     * @param mixed $value the value of the input parameter
     * @param mixed $compare the value to compare to
     * @return mixed
     */
    public static function validateMinLength($value, $compare) {
        return (mb_strlen($value) >= $compare);
    }

    /**
     * Validates that the input value has a maximum length of the given parameter
     *
     * @param mixed $value the value of the input parameter
     * @param mixed $compare the value to compare to
     * @return mixed
     */
    public static function validateMaxLength($value, $compare) {
        return (mb_strlen($value) <= $compare);
    }

    /**
     * Validates that the input value has the exact length of the given parameter
     *
     * @param mixed $value the value of the input parameter
     * @param mixed $compare the value to compare to
     * @return mixed
     */
    public static function validateExactLength($value, $compare) {
        return (mb_strlen($value) == $compare);
    }

    /**
     * Validates that the input value is an e-mail address
     *
     * @param mixed $value the value of the input parameter
     * @return mixed
     */
    public static function validateEmail($value) {
        $regexp = '/^[A-Z0-9._%+-]+@(?:[A-Z0-9-]+\.)+[A-Z]{2,4}$/i';
        return (bool) preg_match($regexp, $value);
    }

    /**
     * Validates that the input value matches another input parameter
     *
     * @param mixed $value the value of the input parameter
     * @param mixed $compareField the name of the input parameter to compare to
     * @param mixed $instance the instance of the Input object
     * @return mixed
     */
    public static function validateMatches($value, $compareField, $instance) {
        if (isset($instance->_fields[$compareField])) {
            return ($value == $instance->_fields[$compareField][self::PARAM_VALUE]);
        }
    }

    /**
     * Validates that the input value is an url
     *
     * @param mixed $value the value of the input parameter
     * @return mixed
     */
    public static function validateUrl($value) {
        if (($url = parse_url($value)) && !empty($url['scheme']) && !empty($url['host'])) {
            return true;
        }
        return false;
    }

    /**
     * Sets the field to a default value if the input value is empty
     *
     * @param mixed $value the value of the input parameter
     * @param mixed $defaultValue the default value to assign if the input value is empty
     * @return mixed
     */
    public static function validateDefault($value, $defaultValue) {
        if (empty($value)) {
            return $defaultValue;
        }
        return $value;
    }

}
