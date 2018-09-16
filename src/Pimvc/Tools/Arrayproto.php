<?php

/**
 * class Tools_Array
 *
 * Tools for array management.
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 * @copyright Pier-Infor
 * @version 1.0
 */
namespace Pimvc\Tools;

class Arrayproto
{

    /**
     * ato returns mixed from array
     *
     * @param array $array
     * @return object
     */
    public static function ato($array)
    {
        if (is_array($array)) {
            foreach ($array as &$item) {
                $item = self::ato($item);
            }
            return (object) $array;
        }
        return $array;
    }

    /**
     * ota : returns array from mixed (useable simplexml)
     *
     * @param mixed $arrObjData
     * @param array $arrSkipIndices
     */
    public static function ota($arrObjData, $arrSkipIndices = array())
    {
        $arrData = array();

        // if input is object, convert into array
        if (is_object($arrObjData)) {
            $arrObjData = get_object_vars($arrObjData);
        }

        if (is_array($arrObjData)) {
            foreach ($arrObjData as $index => $value) {
                if (is_object($value) || is_array($value)) {
                    $value = self::ota($value, $arrSkipIndices); // recursive call
                }
                if (in_array($index, $arrSkipIndices)) {
                    continue;
                }
                $arrData[$index] = $value;
            }
        }
        return $arrData;
    }

    /**
     * sortBySubkey sorts an array from a given array subkey and sort order
     *
     * @param array $array
     * @param string $subkey
     * @param string $sortType
     */
    public static function sortBySubkey(&$array, $subkey, $sortType = SORT_DESC)
    {
        $keys = array();
        foreach ($array as $subarray) {
            $keys[] = $subarray[$subkey];
        }
        array_multisort($keys, $sortType, $array);
    }
    
    /**
     * conciliate returns $data key & values, intersecting $col key on $data keys
     *
     * @param array $data
     * @param array $cols
     * @return array
     */
    public static function conciliate($data, $cols)
    {
        $dataCols = array_fill_keys($cols, '');
        return array_intersect_key($data, $dataCols);
    }
    

    /**
     * conciliateLot returns concialiate for n $rows array
     *
     */
    public static function conciliateLot($data, $cols)
    {
        $result = array();
        foreach ($data as $row) {
            $result[] = self::conciliate($row, $cols);
        }
        return $result;
    }
    
    /**
     * mergeAssoc
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public static function mergeAssoc($array1, $array2)
    {
        return array_combine(
            array_merge(array_keys($array1), array_keys($array2)),
            array_merge(
                array_values($array1),
                array_values($array2)
            )
        );
    }
    
    /**
     * array_column
     *
     * @param type $input
     * @param type $columnKey
     * @param type $indexKey
     * @return null|boolean
     */
    public static function array_column($input = null, $columnKey = null, $indexKey = null)
    {
        // Using func_get_args() in order to check for proper number of
        // parameters and trigger errors exactly as the built-in array_column()
        // does in PHP 5.5.
        $argc = func_num_args();
        $params = func_get_args();

        if ($argc < 2) {
            trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
            return null;
        }

        if (!is_array($params[0])) {
            trigger_error('array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given', E_USER_WARNING);
            return null;
        }

        if (!is_int($params[1])
            && !is_float($params[1])
            && !is_string($params[1])
            && $params[1] !== null
            && !(is_object($params[1]) && method_exists($params[1], '__toString'))
        ) {
            trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        if (isset($params[2])
            && !is_int($params[2])
            && !is_float($params[2])
            && !is_string($params[2])
            && !(is_object($params[2]) && method_exists($params[2], '__toString'))
        ) {
            trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        $paramsInput = $params[0];
        $paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;

        $paramsIndexKey = null;
        if (isset($params[2])) {
            if (is_float($params[2]) || is_int($params[2])) {
                $paramsIndexKey = (int) $params[2];
            } else {
                $paramsIndexKey = (string) $params[2];
            }
        }

        $resultArray = array();

        foreach ($paramsInput as $row) {
            $key = $value = null;
            $keySet = $valueSet = false;

            if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
                $keySet = true;
                $key = (string) $row[$paramsIndexKey];
            }

            if ($paramsColumnKey === null) {
                $valueSet = true;
                $value = $row;
            } elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
                $valueSet = true;
                $value = $row[$paramsColumnKey];
            }

            if ($valueSet) {
                if ($keySet) {
                    $resultArray[$key] = $value;
                } else {
                    $resultArray[] = $value;
                }
            }
        }

        return $resultArray;
    }

    /**
     * recursive_array_search
     *
     * @param string $needle
     * @param array $haystack
     * @return boolean || int
     */
    public static function recursive_array_search($needle, $haystack)
    {
        foreach ($haystack as $key => $value) {
            $current_key = $key;
            if ($needle === $value
                    || (is_array($value)
                    && self::recursive_array_search($needle, $value) !== false)
                    ) {
                return $current_key;
            }
        }
        return false;
    }
    
    /**
     * rotateMatrix
     *
     * @param type $matrix
     * @return type
     */
    public static function rotateMatrix($matrix)
    {
        $rows = count($matrix);
        $cols = count($matrix[0]); // assumes non empty matrix
        $ridx = 0;
        $cidx = 0;
        $out = array();
        foreach ($matrix as $rowidx => $row) {
            foreach ($row as $colidx => $val) {
                $out[$ridx][$cidx] = $val;
                $ridx++;
                if ($ridx >= $rows) {
                    $cidx++;
                    $ridx = 0;
                }
            }
        }
        return $out;
    }
    
    
    /**
     * recursive_array_diff
     *
     * @param array $a1
     * @param array $a2
     * @return array
     */
    public static function recursive_array_diff($a1, $a2)
    {
        $r = array();
        foreach ($a1 as $k => $v) {
            if (array_key_exists($k, $a2)) {
                if (is_array($v)) {
                    $rad = self::recursive_array_diff($v, $a2[$k]);
                    if (count($rad)) {
                        $r[$k] = $rad;
                    }
                } else {
                    if ($v != $a2[$k]) {
                        $r[$k] = $v;
                    }
                }
            } else {
                $r[$k] = $v;
            }
        }
        return $r;
    }
}
