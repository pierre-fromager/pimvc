<?php

/**
 * Model_Domain_Mysql_Constraints
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
class Model_Domain_Mysql_Constraints extends Lib_Db_Model_Domain_Abstract {

    /**
     * @var string $constraint_catalog .
     * @length 512 
     */
    public $constraint_catalog;
    
    /**
     * @var string $constraint_schema .
     * @length 64
     */
    public $constraint_schema;
    
    /**
     * @var string $constraint_name .
     * @length 64
     */
    public $constraint_name;
    
    /**
     *
     * @var string $table_schema .
     * @length 64
     */
    public $table_schema;
    
    /**
     *
     * @var string $table_name .
     * @length 64
     */
    public $table_name;
    
    /**
     *
     * @var string $constraint_type .
     * @length 64
     */
    public $constraint_type;

}

