<?php

/**
 * Description of Lib_Validator_Abstract
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc;

class Validator
{
    const VALIDATOR_SPLITER = '_';

    /**
     * isShortDate returns true if value date is DD/MM/YYYY valid
     *
     * @param type $value
     * @return type
     */
    public static function isShortDate($value)
    {
        $dp = preg_split("/[\s\/]+/", $value);
        $lengthsValid = (
            strlen($dp[0]) == 2
        )
            && (strlen($dp[1]) == 2)
            && (strlen($dp[2]) == 4);
        return $lengthsValid && checkdate(
            $dp[1], // Month
            $dp[0], // Day
            $dp[2] // Year
        );
    }
    
    /**
     * isUsDateTime
     *
     * @param string $value
     * @return boolean
     */
    public static function isUsDateTime($value, $separator = ' ')
    {
        $parts = explode($separator, $value);
        $isDateTime = false;
        if (count($parts) == 2) {
            $isDateTime = self::isUsDate($parts[0]) && self::isTime($parts[1]);
        }
        return $isDateTime;
    }
    
    /**
     * isUsDate
     *
     * @param string $value
     * @return boolean
     */
    public static function isUsDate($value, $separator = '-')
    {
        return preg_match(self::getUsDatePattern($separator), $value);
    }
    
    /**
     * getUsDatePattern
     *
     * @return string
     */
    private static function getUsDatePattern($separator = '-')
    {
        return '/^[0-9]{4}' . $separator . '(0[1-9]|1[0-2])'
            . $separator . '(0[1-9]|[1-2][0-9]|3[0-1])$/';
    }

    /**
     * isTime
     *
     * @param string $value
     * @return boolean
     */
    public static function isTime($value, $separator = ':')
    {
        return preg_match(self::getTimePattern($separator), $value);
    }
    
    /**
     * getTimePattern
     *
     * @return string
     */
    private static function getTimePattern($separator = ':')
    {
        return '/^([01][0-9]|2[0-3])' . $separator . '([0-5][0-9])'
            . $separator . '([0-5][0-9])$/';
    }

    /**
     * isRequired returns true if not empty value
     *
     * @param string $value
     * @return boolean
     */
    public static function isRequired($value)
    {
        return !empty($value);
    }

    /**
     * isbool returns true if $value is boolean
     *
     * @param mixed $param
     * @return boolean
     */
    public static function isbool($value)
    {
        $isBoolChecker = array('true', 'false');
        return in_array($value, $isBoolChecker);
    }
    
    /**
     * isboolNotRequired returns true if $value is boolean
     *
     * @param mixed $param
     * @return boolean
     */
    public static function isBoolNotRequired($value)
    {
        $isBoolChecker = array('true', 'false');
        $isboolNotRequired = (!empty($value))
            ? in_array($value, $isBoolChecker)
            : true;
        return $isboolNotRequired;
    }


    /**
     * isalpha returns true if $value is alphanumeric value
     *
     * @param mixed $param
     * @return boolean
     */
    public static function isAlpha($value)
    {
        return ctype_alpha($value);
    }

    /**
     * isint returns true if $value is integer value
     *
     * @param mixed $value
     * @return boolean
     */
    public static function isInt($value)
    {
        return is_int($value);
    }
    
    /**
     * isnumeric returns true if $value is a numeric value
     *
     * @param mixed $value
     * @return boolean
     */
    public static function isNumeric($value)
    {
        return is_numeric($value);
    }
    
    /**
     * isNumericNotRequired returns true if $value is a numeric value
     *
     * @param mixed $value
     * @return boolean
     */
    public static function isNumericNotRequired($value)
    {
        return (!empty($value)) ? is_numeric($value) : true;
    }
    
    /**
     * isCorrectIpV4
     *
     * @param string $value
     * @return boolean
     */
    public static function isCorrectIpV4($value)
    {
        return preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $value);
    }


    /**
     * isIp
     *
     * @param string $value
     * @param boolean $isPublic
     * @param boolean $isIp4
     * @param boolean $isReserved
     * @return boolean
     */
    public static function isIp($value, $isPublic = true, $isIp4 = true, $isReserved = false)
    {
        $flags = null;
        if ($isPublic) {
            $flags = FILTER_FLAG_NO_PRIV_RANGE;
        }
        if ($isIp4) {
            $flags = ($flags)
                ? $flags | FILTER_FLAG_IPV4
                : FILTER_FLAG_IPV4;
        } else {
            $flags = ($flags)
                ? $flags | FILTER_FLAG_IPV6
                : FILTER_FLAG_IPV6;
        }
        if ($isReserved) {
            $flags = ($flags)
                ? $flags | FILTER_FLAG_NO_RES_RANGE
                : FILTER_FLAG_NO_RES_RANGE;
        }
        $result = filter_var(
            $value,
            FILTER_VALIDATE_IP,
            $flags
        );
        return ($result != $value) ? false : true;
    }
    
    /**
     * isIpV4
     *
     * @param string $value
     * @return boolean
     */
    public static function isIpV4($value)
    {
        if (!self::isCorrectIpV4($value)) {
            return false;
        }
        $result = filter_var(
            $value,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_IPV4
        );
        return ($result != false) ? true : false;
    }
    
    /**
     * isIpV6
     *
     * @param string $value
     * @return boolean
     */
    public static function isIpV6($value)
    {
        $result = filter_var(
            $value,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_IPV6
        );
        return ($result != false) ? true : false;
    }
    
    /**
     * isIpReserved
     *
     * @param string $value
     * @return boolean
     */
    public static function isIpReserved($value)
    {
        if (is_bool($value)) {
            return false;
        }
        if (!self::isCorrectIpV4($value)) {
            return false;
        }
        $result = filter_var(
            $value,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_RES_RANGE
        );
        return ($result === false) ? true : false;
    }
    
    /**
     * isIpMulticast
     *
     * @param string $value
     * @return boolean
     */
    public static function isIpMulticast($value)
    {
        return Tools_Network_Cidr::IPisWithinCIDR($value, '224.0.0.0/4');
    }

    /**
     * isIpPublic
     *
     * @param string $value
     * @return boolean
     */
    public static function isIpPublic($value)
    {
        $result = filter_var(
            $value,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE
        );
        return ($result !== false) ? true : false;
    }
    
    /**
     * isIpInNet
     *
     * @param string $ip
     * @param string $network
     * @param string $mask
     * @return boolean
     */
    public static function isIpInNet($ip, $network, $mask)
    {
        return Tools_Validators_Net_Ip::isInNet($ip, $network, $mask);
    }
    
    /**
     * isIpInRange
     *
     * @param string $ip
     * @param string $netStart
     * @param string $netStop
     * @return boolean
     */
    public static function isIpInRange($ip, $netStart, $netStop)
    {
        return Tools_Validators_Net_Ip::isInRange($ip, $netStart, $netStop);
    }
    
    /**
     * isIpInCidr
     *
     * @param string $ip
     * @param string $cidr
     * @return boolean
     */
    public static function isIpInCidr($ip, $cidr)
    {
        return Tools_Validators_Net_Ip::isInCidr($ip, $cidr);
    }
    
    /**
     * isurl returns true if $value is an url value
     *
     * @param mixed $value
     * @return boolean
     */
    public static function isUrl($value)
    {
        return (ereg("^[a-zA-Z0-9\-\.]+\.(com|org|net|mil|edu)$", $value));
    }
    
    /**
     * isUrlFv returns true if $value is an url value
     *
     * @param mixed $value
     * @return boolean
     */
    public static function isUrlFv($value)
    {
        return filter_var($value, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED);
    }
    
    /**
     * isEmail returns true if given email value is valid
     *
     * @param string $email
     * @return boolean
     */
    public static function isEmail($value)
    {
        if (!$value) {
            return false;
        }
        $atom = '[-a-z0-9!#$%&\'*+\\/=?^_`{|}~]';
        $domain = '([a-z0-9]([-a-z0-9]*[a-z0-9]+)?)';
        $regex = '/^' . $atom . '+' . '(\.' . $atom . '+)*' . '@' . '(' . $domain
            . '{1,63}\.)+' . $domain . '{2,63}$/i';
        return preg_match($regex, $value);
    }
    
    /**
     * isEmailFv returns true if given email value is valid
     *
     * @param string $email
     * @return boolean
     */
    public static function isEmailFv($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }
    
    /**
     * isInRange return true if  ( loRef < value < hiRef ).
     * If $toLower => $loRef, $hiRef , $value will be rounded to floor or ceil.
     *
     * @param int $loRef
     * @param int $hiRef
     * @param int $value
     * @return boolean
     */
    public static function isInRange($loRef, $hiRef, $value, $toLower = true)
    {
        $options = array(
            "options"=> array(
                "min_range"=> ($toLower) ? floor($loRef) : ceil($loRef)
                , "max_range"=> ($toLower) ? floor($hiRef) : ceil($hiRef)
        ));
        return (filter_var(
            ($toLower) ? floor($value) : ceil($value),
            FILTER_VALIDATE_INT,
            $options
        ));
    }
    
    /**
     * isMaxLen
     *
     * @param mixed $value
     * @param int $max
     * @return boolean
     */
    public static function ismaxlen($value, $max)
    {
        $isLower = (strlen($value) <= $max);
        return $isLower;
    }
    
    /**
     * isMinLen
     *
     * @param mixed $value
     * @param int $min
     * @return boolean
     */
    public static function isminlen($value, $min)
    {
        $isGreater = (strlen($value) >= $min);
        return $isGreater;
    }
    
    /**
     * islenbetween
     *
     * @param mixed $value
     * @param string $min_max as min_max
     * @return boolean
     */
    public static function islenbetween($value, $min_max)
    {
        $isLenBetween = false;
        if (self::getMinMax($min_max) !== false) {
            $minMax = self::getMinMax($min_max);
            $isMin = self::isminlen($value, $minMax['min']);
            $isMax = self::ismaxlen($value, $minMax['max']);
            $isLenBetween = ($isMin && $isMax);
        }
        return $isLenBetween;
    }
    
    /**
     * ispassword
     *
     * Match password with min-max chars with letters and digits
     *
     * @param string $value
     * @return boolean
     */
    public static function ispassword($value, $min_max)
    {
        $isPassword = false;
        if (self::getMinMax($min_max) !== false) {
            $minMax = self::getMinMax($min_max);
            $min = $minMax['min'];
            $max = $minMax['max'];
            $pattern = '/'
                . '^'               // Anchor to start of string.
                . '(?=.*?[A-Za-z])' // Assert there is at least one letter, AND
                . '(?=.*?[0-9])'    // Assert there is at least one digit, AND
                . '(?=.{' . $min . ',' . $max . '}' // Assert the length is from $min to $max chars.
                . '\z)/x';
            $isPassword = preg_match($pattern, $value);
        }
        return $isPassword;
    }
    
    /**
     * getMinMax
     *
     * @param string $min_max
     * @return array|boolean
     */
    private static function getMinMax($min_max)
    {
        $minMax = false;
        if (strpos($min_max, self::VALIDATOR_SPLITER) !== false) {
            $params = explode(self::VALIDATOR_SPLITER, $min_max);
            $minMax = array(
                'min' => $params[0]
                , 'max' => $params[1]
            );
        }
        return $minMax;
    }
    
    /**
     * validxcsrf
     *
     * @param string $xcsrf
     * @return boolean
     */
    public static function validxcsrf($posted)
    {
        $data = [];
        $data[Form::FORM_XCSRF] = $posted;
        Lib_Form_Csrf::enableOriginCheck();
        $validxcsrf = Lib_Form_Csrf::check(
            Form::FORM_XCSRF,
            $data,
            2
        );
        
        foreach (Lib_Form_Csrf::getErrors() as $error) {
            Tools_Flash::addError($error);
        }
        return $validxcsrf;
    }
    
    /**
     * isuploaded
     *
     * @param string $filename
     * @return boolean
     */
    public static function isuploaded($filename)
    {
        return is_uploaded_file($filename);
    }
    
    /**
     * isRss
     *
     * @param string $url
     * @return boolean
     */
    public static function isRss($url)
    {
        $urlValidator = 'http://feedvalidator.org/check.cgi?url='
            . urlencode($url);
        $validResponse = 'This is a valid RSS feed';
        if ($validationResponse = @file_get_contents($urlValidator)) {
            if (stristr($validationResponse, $validResponse) !== false) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
