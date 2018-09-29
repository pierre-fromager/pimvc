<?php
/**
 * Pimvc\Model\Pgsql\Domain\Tables
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Model\Pgsql\Domain;

class Tables extends \Pimvc\Db\Model\Domain
{

    public $schemaname;
    public $tablename;
    public $tableowner;
    public $tablespace;
    public $hasindexes;
    public $hasrules;
    public $hastriggers;
    public $rowsecurity;

}
