<?php
/**
 * Pimvc\Model\Fourd\Domain\Conscolumns
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Model\Fourd\Domain;

class Conscolumns extends \Pimvc\Db\Model\Domain
{

    public $constraint_id;
    public $constraint_name;
    public $table_name;
    public $table_id;
    public $column_name;
    public $column_id;
    public $column_position;
    public $related_column_name;
    public $related_column_id;
}
