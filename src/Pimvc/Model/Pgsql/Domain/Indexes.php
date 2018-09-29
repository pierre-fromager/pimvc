<?php
/**
 * Pimvc\Model\Pgsql\Domain\Indexes
 */
namespace Pimvc\Model\Pgsql\Domain;

class Indexes extends \Pimvc\Db\Model\Domain
{

    public $indexrelid;
    public $indrelid;
    public $indnatts;
    public $indisunique;
    public $indisprimary;
    public $indisexclusion;
    public $indimmediate;
    public $indisclustered;
    public $indisvalid;
    public $indcheckxmin;
    public $indisready;
    public $indislive;
    public $indisreplident;
    public $indkey;
    public $indcollation;
    public $indclass;
    public $indoption;
    public $indexprs;
    public $indpred;
}
