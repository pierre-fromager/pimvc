<?php
/**
 * Tools_Db_Generate_Model
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\Db\Generate;

class Model {

    const TYPE_PHP_START = '<?php';
    const TYPE_ADAPTER = 'Pdo4d';
    const MYSQL_ADAPTER = 'PdoMysql';
    const DEFAULT_ADAPTER = self::MYSQL_ADAPTER;
    const TYPE_PROPRETY_PUBLIC = 'public';
    const TYPE_CLASS = 'class';
    const GENERATE_MODEL_PREFIX = 'Model_';
    const GENERATE_EXTENDS = 'extends';
    const GENERATE_MODEL_SUFFIX = 'Lib_Db_Model_Abstract';
    const GENERATE_O_BRACKET = '{';
    const GENERATE_C_BRACKET = '}';
    const GENERATE_COMA = ';';
    const GENERATE_TAB = "\t";
    const GENERATE_QUOTE = "'";
    const GENERATE_FUNCTION = 'function';
    const GENERATE_PUBLIC = 'public';
    const GENERATE_PROTECTED = 'protected';
    const GENERATE_PARENT = 'parent';
    const GENERATE_CONSTRUCT = '__construct';
    const GENERATE_ARRAY = 'array';
    const GENERATE_PLURAL = 's';
    const ADAPTER_4D = 'Pdo4d';
    
    protected static $indexes = array();
    protected static $relations = array();
    protected static $dependentModels = '';
    protected static $tableName = '';
    protected static $adapter = '';
    protected static $modelSuffix;

    /**
     * get
     *
     * @param string $tableName
     * @param array $colomns
     */
    public static function get($adapter = '', $tableName, $indexes, $relations = array()) {
        self::$adapter = (empty($adapter)) 
            ? self::DEFAULT_ADAPTER 
            : $adapter;
        self::$modelSuffix = (self::$adapter == self::ADAPTER_4D) 
            ? self::GENERATE_MODEL_PREFIX . 'Proscope_' 
            : self::GENERATE_MODEL_PREFIX;
        self::$tableName = $tableName;
        self::$indexes = array();
        self::$relations = array();
        $formatedClass = self::TYPE_PHP_START . PHP_EOL 
            . self::getClassLine($tableName) 
            . self::getVars($indexes, $relations) 
            . self::GENERATE_C_BRACKET;
        $result = '<font size="1">' 
            . str_replace('style="color: "', '' , highlight_string($formatedClass, true)) 
            . '</font>';
        return $result;
    }

    /**
     * getClassLine
     *
     * @param string $tableName
     * @return string
     */
    private static function getClassLine($tableName) {
        return self::TYPE_CLASS . ' ' . self::$modelSuffix
            . ucfirst(str_replace('_', '', strtolower($tableName))) . self::GENERATE_PLURAL
            . ' ' . self::GENERATE_EXTENDS . ' ' . self::GENERATE_MODEL_SUFFIX
            . ' '. self::GENERATE_O_BRACKET . PHP_EOL;
    }

    /**
     * isAssoc
     *
     * @param array $array
     * @return boolean
     */
    private static function isAssoc($array) {
        $array = array_keys($array);
        return ($array !== array_keys($array));
    }


    /**
     * isIndex
     * 
     * @param string $columnName
     * @return boolean 
     */
    private static function isIndex($columnName) {
        return in_array($columnName, self::$indexes);
    }
    
    /**
     * setIndexes
     * 
     * @param array $indexes 
     */
    private static function setIndexes($indexes) {
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
    private static function isPk($columnName) {
        return isset(self::$relations[$columnName]);
    }
    
    /**
     * getFt
     * 
     * @param string $columnName
     * @return string 
     */
    private static function getFt($columnName) {
        return self::$relations[$columnName]['table_name'];
    }
    
    /**
     * getModelClassname
     * 
     * @param string $tableName
     * @return string 
     */
    private static function getModelClassname($tableName) {
        return ucfirst(strtolower(str_replace('_', '', $tableName)));
    }

    /**
     * getFts
     * 
     * @return array 
     */
    private static function getFts() {
        $fts = array();
        foreach (self::$relations as $k => $v) {
            $fts[] = self::getFt($k) . self::GENERATE_PLURAL;
        }
        $fts = array_unique($fts);
        $fts = array_map(__CLASS__ . '::getModelClassname', $fts);
        return $fts;
    }
    
    
    /**
     * getFk
     * 
     * @param string $columnName
     * @return string 
     */
    private static function getFk($columnName) {
        return self::$relations[$columnName]['column_name'];
    }
    
    /**
     * setRelations
     * 
     * @param array $indexes 
     */
    private static function setRelations($relations) {
        foreach ($relations as $k => $v) {
            $name = $v[0];
            self::$relations[$name] = array(
                'table_name' => $v[1]
                , 'column_name' => $v[2]
            );
        }
    }
    
    /**
     * getDeclarationVar
     * 
     * @param string $type
     * @param string $name
     * @param string $value
     * @return string 
     */
    private static function getDeclarationVar($type, $name, $value, $withQuote = true) {
        $quote = ($withQuote) ? self::GENERATE_QUOTE : '';
        return self::GENERATE_TAB
            . $type . ' ' . $name . ' = ' . $quote . $value . $quote 
            . self::GENERATE_COMA . PHP_EOL;
    }
    
    /**
     * quoteWrapper is a callback function quote mapper
     * @param string $value
     */
    private static function quoteMapper($value) {
        return self::GENERATE_QUOTE . $value . self::GENERATE_QUOTE;
    }
        
    /**
     * getdependentModels
     * 
     * @return string 
     */
    private static function getRefmap() {
        $refmap = array();
        foreach (self::$relations as $key => $value) {
            $local = $key;
            $foreign = $value['column_name'];
            $normaliseTableName = strtolower(str_replace('_', '', $value['table_name']));
            $table = self::$modelSuffix . ucfirst($normaliseTableName) . 's';
            $refmap[$table] = array(
                'local' => $local
                , 'foreign' => $foreign
                , 'alias' => $normaliseTableName
                , 'table' => $value['table_name']
            );
        }
        return self::getDeclarationVar(
            self::GENERATE_PROTECTED
            , '$_refMap'
            , var_export($refmap, true)
            , false
        );
    }
    
    /**
     * getName
     * 
     * @return string 
     */
    private static function getName() {
        return self::getDeclarationVar(
            self::GENERATE_PROTECTED
            , '$_name'
            , self::$tableName
        );
    }
    
    /**
     * getPrimary
     * 
     * @return string 
     */
    private static function getPrimary() {
        return self::getDeclarationVar(
            self::GENERATE_PROTECTED
            , '$_primary'
            , self::$indexes[0]
        );
    }
    
    /**
     * getAdapter
     * 
     * @return string 
     */
    private static function getAdapter() {
        return self::getDeclarationVar(
            self::GENERATE_PROTECTED
            , '$_adapter'
            , self::$adapter
        );
    }
    
    /**
     * getAlias
     * 
     * @return string 
     */
    private static function getAlias() {
        $alias = strtolower(str_replace('_', '', self::$tableName));
        return self::getDeclarationVar(
            self::GENERATE_PROTECTED
            , '$_alias'
            , $alias
        );
    }
    
    /**
     * getDeclarationFunction
     * 
     * @param string $type
     * @param string $name
     * @param string $value
     * @return string 
     */
    private static function getDeclarationFunction($type, $name, $params, $lines) {
        return PHP_EOL . self::GENERATE_TAB
            . $type . ' '. self::GENERATE_FUNCTION . ' ' . $name . '(' . $params . ')' 
            . self::GENERATE_O_BRACKET . PHP_EOL
            . self::GENERATE_TAB . self::GENERATE_TAB . $lines . PHP_EOL
            . self::GENERATE_TAB . self::GENERATE_C_BRACKET . PHP_EOL;
    }
    
    /**
     * getContructor
     * 
     * @return string 
     */
    private static function getContructor() {
        $name = self::GENERATE_CONSTRUCT;
        $params = '$config = array()';
        $lines = 'parent::__construct($config);';
        $type = self::GENERATE_PUBLIC;
        return self::getDeclarationFunction($type, $name, $params, $lines);
    }

    /**
     * getVars
     *
     * @param string $tableName
     */
    private static function getVars($indexes, $relations) {
        self::setIndexes($indexes);
        self::setRelations($relations);
        return PHP_EOL . self::getName() 
            . self::getPrimary()
            . self::getAlias()
            . self::getAdapter()
            . self::getRefmap()
            . self::getContructor();
    }
}