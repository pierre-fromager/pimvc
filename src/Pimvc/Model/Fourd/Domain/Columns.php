<?php
/**
 * Pimvc\Model\Fourd\Domain\Columns
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Model\Fourd\Domain;

class Columns extends \Pimvc\Db\Model\Domain
{

    public $table_name;
    public $column_name;
    public $data_type;
    public $data_length;
    public $nullable;
    public $table_id;
    public $column_id;
    public $old_data_type;
}
