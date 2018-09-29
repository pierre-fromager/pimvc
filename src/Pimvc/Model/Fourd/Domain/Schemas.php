<?php
/**
 * Pimvc\Model\Fourd\Domain\Schemas
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Model\Fourd\Domain;

class Schemas extends \Pimvc\Db\Model\Domain
{

    public $schema_id;
    public $schema_name;
    public $read_group_id;
    public $read_group_name;
    public $read_write_group_id;
    public $read_write_group_name;
    public $all_group_id;
    public $all_group_name;
}
