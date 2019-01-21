<?php

namespace Pimvc\Tools\Db\Fourd;

class Types
{
    
    const TYPE_BOOLEAN = 1;
    const TYPE_BOOLEAN_LABEL = 'boolean';
    const TYPE_INTEGER = 3;
    const TYPE_INTEGER_LABEL = 'integer';
    const TYPE_LONGINT = 4;
    const TYPE_LONGINT_LABEL = 'longint';
    const TYPE_REAL = 6;
    const TYPE_REAL_LABEL = 'float';
    const TYPE_DATE = 8;
    const TYPE_DATE_LABEL = 'date';
    const TYPE_TIME = 9;
    const TYPE_TIME_LABEL = 'time';
    const TYPE_STRING = 10;
    const TYPE_STRING_LABEL = 'string';
    const TYPE_LOB = 12;
    const TYPE_LOB2 = 18;
    const TYPE_LOB_LABEL = 'lob';
    const TYPE_NULL_LABEL = 'null';
    const TYPE_STMT_LABEL = 'statement';
    const TYPE_UNKNOWN_LABEL = 'unknown';
    const TYPE_4D_INDEX_BTREE = 1;
    const TYPE_4D_INDEX_BTREE_LABEL = 'b-tree';
    const TYPE_4D_INDEX_CLUSTER_BTREE = 3;
    const TYPE_4D_INDEX_CLUSTER_BTREE_LABEL = 'cluster b-tree';

    /**
     * getPdo returns the pdo binding's type for a given 4d type
     *
     * @param int $type4d
     * @return int
     */
    public static function getPdo($type4d): int
    {
        $pdo = (self::isFourdInt($type4d)) ? \PDO::PARAM_INT : \PDO::PARAM_STR;
        $pdo = (self::isFourdBool($type4d)) ? \PDO::PARAM_BOOL : $pdo;
        $pdo = (self::isFourdLob($type4d)) ? \PDO::PARAM_LOB : $pdo;
        return $pdo;
    }

    /**
     * isFourdInt
     *
     * @param int $type4d
     * @return bool
     */
    public static function isFourdInt(int $type4d): bool
    {
        return in_array($type4d, [self::TYPE_INTEGER, self::TYPE_LONGINT]);
    }

    /**
     * isFourdFloat
     *
     * @param int $type4d
     * @return bool
     */
    public static function isFourdFloat(int $type4d): bool
    {
        return ($type4d === self::TYPE_REAL);
    }

    /**
     * isFourdDate
     *
     * @param int $type4d
     * @return bool
     */
    public static function isFourdDate(int $type4d): bool
    {
        return ($type4d === self::TYPE_DATE);
    }

    /**
     * isFourdTime
     *
     * @param int $type4d
     * @return bool
     */
    public static function isFourdTime(int $type4d): bool
    {
        return ($type4d === self::TYPE_TIME);
    }

    /**
     * isFourdBool
     *
     * @param int $type4d
     * @return bool
     */
    public static function isFourdBool(int $type4d): bool
    {
        return ($type4d === self::TYPE_BOOLEAN);
    }

    /**
     * isFourdLob
     *
     * @param int $type4d
     * @return bool
     */
    public static function isFourdLob(int $type4d): bool
    {
        return in_array($type4d, [self::TYPE_LOB, self::TYPE_LOB2]);
    }

    /**
     * getLabel
     *
     * @param int $type4d
     * @return string
     */
    public static function getLabel(int $type4d): string
    {
        $types = [
            self::TYPE_BOOLEAN => self::TYPE_BOOLEAN_LABEL
            , self::TYPE_INTEGER => self::TYPE_INTEGER_LABEL
            , self::TYPE_LONGINT => self::TYPE_LONGINT_LABEL
            , self::TYPE_REAL => self::TYPE_REAL_LABEL
            , self::TYPE_DATE => self::TYPE_DATE_LABEL
            , self::TYPE_TIME => self::TYPE_TIME_LABEL
            , self::TYPE_STRING => self::TYPE_STRING_LABEL
            , self::TYPE_LOB => self::TYPE_LOB_LABEL
            , self::TYPE_LOB2 => self::TYPE_LOB_LABEL
        ];
        $numericValue = '&nbsp;(' . $type4d . ')';
        return (isset($types[$type4d])) ? ucfirst($types[$type4d]) . $numericValue : ucfirst(self::TYPE_UNKNOWN_LABEL) . $numericValue;
    }

    /**
     * getPdoLabel
     *
     * @param int $typePdo
     * @return string
     */
    public static function getPdoLabel(int $typePdo): string
    {
        $types = [
            \PDO::PARAM_NULL => self::TYPE_NULL_LABEL
            , \PDO::PARAM_INT => self::TYPE_INTEGER_LABEL
            , \PDO::PARAM_STR => self::TYPE_STRING_LABEL
            , \PDO::PARAM_LOB => self::TYPE_LOB_LABEL
            , \PDO::PARAM_STMT => self::TYPE_STMT_LABEL
            , \PDO::PARAM_BOOL => self::TYPE_BOOLEAN_LABEL
        ];
        $numericValue = '&nbsp;(' . $typePdo . ')';
        return (isset($types[$typePdo])) ? ucfirst($types[$typePdo]) . $numericValue : ucfirst(self::TYPE_UNKNOWN_LABEL) . $numericValue;
    }

    /**
     * get returns 4d types, 4d lenghts , Pdo type
     *
     * @param string $tableName
     * @param string $columnName
     * @return array
     */
    public static function get(string $tableName): array
    {
        $output = array();
        $typeModel = new Model_4d_Columns();
        $typeResults = $typeModel->getByTableName($tableName);
        foreach ($typeResults as $result) {
            $fieldName = strtolower($result['column_name']);
            $output[$fieldName] = array(
                'length' => $result['data_length']
                , 'type' => $result['data_type']
                , 'pdo_param' => self::getPdo($result['data_type'])
            );
        }
        unset($typeResults);
        unset($typeModel);
        return $output;
    }

    /**
     * getIndexTypeLabel returns 4d index types label
     * for a given 4d index type value
     *
     * @param int $indexTypeValue
     * @return string
     */
    public static function getIndexTypeLabel(int $indexTypeValue): string
    {
        $types = [
            self::TYPE_4D_INDEX_BTREE => self::TYPE_4D_INDEX_BTREE_LABEL
            , self::TYPE_4D_INDEX_CLUSTER_BTREE => self::TYPE_4D_INDEX_CLUSTER_BTREE_LABEL
        ];
        $numericValue = '&nbsp;(' . $indexTypeValue . ')';
        return (isset($types[$indexTypeValue])) ? ucfirst($types[$indexTypeValue]) . $numericValue : ucfirst(self::TYPE_UNKNOWN_LABEL) . $numericValue;
    }
}
