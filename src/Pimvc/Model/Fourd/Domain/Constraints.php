<?php
/**
 * Pimvc\Model\Fourd\Domain\Constraints
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Model\Fourd\Domain;

class Constraints extends \Pimvc\Db\Model\Domain
{

    public $constraint_id;
    public $constraint_name;
    public $constraint_type;
    public $table_name;
    public $table_id;
    public $delete_rule;
    public $related_table_name;
    public $related_table_id;
}
