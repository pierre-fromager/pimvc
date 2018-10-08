<?php

/**
 * Pimvc\Db\Generate\Domain
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Db\Generate;

class Domain
{
    const PARAM_NAME = 'name';
    const PARAM_TYPE = 'type';
    const PARAM_TABLE_NAME = 'table_name';
    const PARAM_COLUMN_NAME = 'column_name';
    const TYPE_PHP_START = '<?php';
    const TYPE_PROPRETY_PUBLIC = 'public';
    const TYPE_CLASS = 'class';
    const GENERATE_MODEL_PREFIX = '';
    const GENERATE_EXTENDS = 'extends';
    const GENERATE_MODEL_NAMESPACE = 'namespace App1\Model\Domain;';
    const GENERATE_MODEL_SUFFIX = '\Pimvc\Db\Model\Domain';
    const GENERATE_O_BRACKET = '{';
    const GENERATE_C_BRACKET = '}';
    const GENERATE_COMA = ';';
    const GENERATE_TAB = "\t";
    const GENERATE_DOCBLOCK_O = '/**';
    const GENERATE_DOCBLOCK_L = '*';
    const GENERATE_DOCBLOCK_C = '*/';
    const GENERATE_PLURAL = 's';
    
    protected static $indexes = [];
    protected static $relations = [];

    /**
     * getClassLine
     *
     * @param string $tableName
     * @return string
     */
    private static function getClassLine($tableName)
    {
        return self::GENERATE_MODEL_NAMESPACE . "\n" . self::TYPE_CLASS . ' ' . self::GENERATE_MODEL_PREFIX
            . ucfirst(str_replace('_', '', strtolower($tableName))) . self::GENERATE_PLURAL
            . ' ' . self::GENERATE_EXTENDS . ' ' . self::GENERATE_MODEL_SUFFIX
            . ' '. self::GENERATE_O_BRACKET . PHP_EOL;
    }

    /**
     * getDocBlock
     *
     * @param string $name
     * @return string
     */
    private static function getDocBlock($column, $indexes, $relations)
    {
        $name = strtolower($column[self::PARAM_NAME]);
        $types = explode('&nbsp;', $column[self::PARAM_TYPE]);
        $typeCode = $types[1];
        $type = $types[0];
        $typePdo = str_replace(array('(',')'), '', $typeCode);
        $length = $column['length'];
        $index = (self::isIndex($name)) ? '1' : '0';
        $pk = (self::isPk($name)) ? '1' : '0';
        $ft = (self::isPk($name)) ? self::getFt($name) : 'null';
        $fk = (self::isPk($name)) ? self::getFk($name) : 'null';
        return self::GENERATE_TAB . self::GENERATE_DOCBLOCK_O . PHP_EOL
            . self::GENERATE_TAB . self::GENERATE_DOCBLOCK_L . ' @var ' . strtolower($type) . ' ' .$name . ' (comments)'. PHP_EOL
            . self::GENERATE_TAB . self::GENERATE_DOCBLOCK_L . ' @name ' . $name . PHP_EOL
            . self::GENERATE_TAB . self::GENERATE_DOCBLOCK_L . ' @type ' . $type . PHP_EOL
            . self::GENERATE_TAB . self::GENERATE_DOCBLOCK_L . ' @pdo ' . $typePdo . PHP_EOL
            . self::GENERATE_TAB . self::GENERATE_DOCBLOCK_L . ' @length ' . $length . PHP_EOL
            . self::GENERATE_TAB . self::GENERATE_DOCBLOCK_L . ' @index ' . $index . PHP_EOL
            . self::GENERATE_TAB . self::GENERATE_DOCBLOCK_L . ' @pk ' . $pk . PHP_EOL
            . self::GENERATE_TAB . self::GENERATE_DOCBLOCK_L . ' @ft ' . $ft . PHP_EOL
            . self::GENERATE_TAB . self::GENERATE_DOCBLOCK_L . ' @fk ' . $fk . PHP_EOL
            . self::GENERATE_TAB . self::GENERATE_DOCBLOCK_C . PHP_EOL;
    }

    /**
     * isIndex
     *
     * @param string $columnName
     * @return boolean
     */
    private static function isIndex($columnName)
    {
        return in_array($columnName, self::$indexes);
    }
    
    /**
     * setIndexes
     *
     * @param array $indexes
     */
    private static function setIndexes($indexes)
    {
        foreach ($indexes as $k => $v) {
            self::$indexes[] = $v[1];
        }
    }
    
    /**
     * isPk
     *
     * @param string $columnName
     * @return boolean
     */
    private static function isPk($columnName)
    {
        return isset(self::$relations[$columnName]);
    }
    
    /**
     * getFt
     *
     * @param string $columnName
     * @return string
     */
    private static function getFt($columnName)
    {
        return self::$relations[$columnName][self::PARAM_TABLE_NAME];
    }
    
    /**
     * getFk
     *
     * @param string $columnName
     * @return string
     */
    private static function getFk($columnName)
    {
        return self::$relations[$columnName][self::PARAM_COLUMN_NAME];
    }
    
    /**
     * setRelations
     *
     * @param array $indexes
     */
    private static function setRelations($relations)
    {
        foreach ($relations as $k => $v) {
            $name = $v[0];
            self::$relations[$name] = array(
                self::PARAM_TABLE_NAME => $v[1]
                , self::PARAM_COLUMN_NAME => $v[2]
            );
        }
    }
    

    /**
     * getVars
     *
     * @param string $tableName
     */
    private static function getVars($columns, $indexes, $relations)
    {
        $vars = '';
        self::setIndexes($indexes);
        self::setRelations($relations);
        foreach ($columns as $column) {
            $vars .= PHP_EOL . self::getDocBlock($column, $indexes, $relations) . self::GENERATE_TAB
                . self::TYPE_PROPRETY_PUBLIC . ' $' . strtolower($column['name'])
                . self::GENERATE_COMA . PHP_EOL;
        }
        return $vars;
    }

    /**
     * get
     *
     * @param string $tableName
     * @param array $colomns
     */
    public static function get($tableName, $columns, $indexes, $relations)
    {
        self::$indexes = [];
        self::$relations = [];
        $result = '<font size="1">' . str_replace('style="color: "', '', highlight_string(self::TYPE_PHP_START . PHP_EOL
            . self::getClassLine($tableName) . self::getVars($columns, $indexes, $relations)
            . self::GENERATE_C_BRACKET, true)) . '</font>';
        return $result;
    }
}
