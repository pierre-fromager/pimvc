<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pimvc\Db\Model\Interfaces;

interface Core
{
    public function run($sql, $bindParams = [], $bindTypes = []);

    public function getQueryType($sql);

    public function getSql();

    public function getSize();
}
