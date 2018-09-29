<?php
/**
 * Pimvc\Model\Fourd\Domain\Indexes
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Model\Fourd\Domain;

class Indexes extends \Pimvc\Db\Model\Domain
{

    public $index_id;
    public $index_name;
    public $index_type;
    public $table_name;
    public $uniqueness;
    public $table_id;
}
