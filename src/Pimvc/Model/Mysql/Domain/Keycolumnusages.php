<?php

/**
 * Model_Domain_Keycolumnusages
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */

namespace Pimvc\Model\Mysql\Domain;

class Keycolumnusages extends \Pimvc\Db\Model\Domain
{

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
     * @var string $constraint_name
     * @length 64
     */
    public $constraint_name;

    /**
     * @var string $table_catalog
     * @length 512
     */
    public $table_catalog;

    /**
     * @var string $table_schema
     * @length 64
     */
    public $table_schema;

    /**
     * @var string $table_name
     * @length 64
     */
    public $table_name;

    /**
     * @var string $column_name
     * @length 64
     */
    public $column_name;

    /**
     * @var int $ordinal_position
     * @length 10
     */
    public $ordinal_position;

    /**
     * @var int $position_in_unique_constraint
     * @length 10
     */
    public $position_in_unique_constraint;

    /**
     * @var string $referenced_table_schema
     * @length 64
     */
    public $referenced_table_schema;

    /**
     * @var string $referenced_table_name
     * @length 64
     */
    public $referenced_table_name;

    /**
     * @var string $referenced_column_name
     * @length 64
     */
    public $referenced_column_name;
}
