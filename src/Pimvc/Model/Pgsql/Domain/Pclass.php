<?php
/**
 * Pimvc\Model\Pgsql\Domain\Pclass
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Model\Pgsql\Domain;

class Pclass extends \Pimvc\Db\Model\Domain
{

    public $relname;
    public $relnamespace;
    public $reltype;
    public $reloftype;
    public $relowner;
    public $relam;
    public $relfilenode;
    public $reltablespace;
    public $relpages;
    public $reltuples;
    public $relallvisible;
    public $reltoastrelid;
    public $relhasindex;
    public $relisshared;
    public $relpersistence;
    public $relkind;
    public $relnatts;
    public $relchecks;
    public $relhasoids;
    public $relhaspkey;
    public $relhasrules;
    public $relhastriggers;
    public $relhassubclass;
    public $relrowsecurity;
    public $relforcerowsecurity;
    public $relispopulated;
    public $relreplident;
    public $relfrozenxid;
    public $relminmxid;
    public $relacl;
    public $reloptions;
}
