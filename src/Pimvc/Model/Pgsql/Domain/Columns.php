<?php
/**
 * Pimvc\Model\Pgsql\Domain\Columns
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Model\Pgsql\Domain;

class Columns extends \Pimvc\Db\Model\Domain
{

    public $table_catalog;
    public $table_schema;
    public $table_name;
    public $column_name;
    public $ordinal_position;
    public $column_default;
    public $is_nullable;
    public $data_type;
    public $character_maximum_length;
    public $character_octet_length;
    public $numeric_precision;
    public $numeric_precision_radix;
    public $numeric_scale;
    public $datetime_precision;
    public $interval_type;
    public $interval_precision;
    public $character_set_catalog;
    public $character_set_schema;
    public $character_set_name;
    public $collation_catalog;
    public $collation_schema;
    public $collation_name;
    public $domain_catalog;
    public $domain_schema;
    public $domain_name;
    public $udt_catalog;
    public $udt_schema;
    public $udt_name;
    public $scope_catalog;
    public $scope_schema;
    public $scope_name;
    public $maximum_cardinality;
    public $dtd_identifier;
    public $is_self_referencing;
    public $is_identity;
    public $identity_generation;
    public $identity_start;
    public $identity_increment;
    public $identity_maximum;
    public $identity_minimum;
    public $identity_cycle;
    public $is_generated;
    public $generation_expression;
    public $is_updatable;
}
