<?php

/**
 * Model_Domain_Mysql_Columns
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
class Model_Domain_Mysql_Columns extends Lib_Db_Model_Domain_Abstract {

    /**
     * @var string $table_catalog .
     * @length 512
     */
    public $table_catalog;
    
    /**
     * @var string $table_schema .
     * @length 64
     */
    public $table_schema;	
    
    /**
     * @var string $table_name .
     * @length 64
     */
    public $table_name;
    
    /**
     * @var string $column_name .
     * @length 64
     */
    public $column_name;
    
    /**
     * @var int $ordinal_position .
     * @length 21
     */
    public $ordinal_position;
    
    /**
     * @var string $column_default .
     * @length 500
     */
    public $column_default;
    
    /**
     * @var string $is_nullable .
     * @length 3
     */
    public $is_nullable;
    
    /**
     * @var string $data_type .
     * @length 64
     */
    public $data_type;
    
    /**
     * @var int $character_maximum_length .
     * @length 21
     */
    public $character_maximum_length;
    
    /**
     * @var int $character_octet_length .
     * @length 21
     */
    public $character_octet_length;
    
    /**
     * @var int $numeric_precision .
     * @length 21
     */
    public $numeric_precision;
    
    /**
     * @var int $numeric_scale .
     * @length 21
     */
    public $numeric_scale;
    
    /**
     * @var string  $character_set_name .
     * @length 32
     */
    public $character_set_name;
    
    /**
     * @var string $collation_name .
     * @length 32
     */
    public $collation_name;
    
    /**
     * @var string $column_type .
     * @length 500
     */
    public $column_type;
    
    /**
     * @var string $column_key .
     * @length 3
     */
    public $column_key;
    
    /**
     * @var string $extra .
     * @length 27
     */
    public $extra;
    
    /**
     * @var string $privileges .
     * @length 80
     */
    public $privileges;
    
    /**
     * @var string $column_comment .
     * @length 255
     */
    public $column_comment;


}

