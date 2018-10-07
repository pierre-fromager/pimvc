<?php

/**
 * Description of Pimvc\Db\Model\Interfaces\Forge
 *
 * @author Pierre Fromager <pf@pier-infor.fr>
 */
namespace Pimvc\Db\Model\Interfaces;

interface Forge
{

    const _DB_POOL = 'dbPool';
    const _ADAPTER = 'adapter';

    public function __construct($slot);

    public function setDb($slot);
}
