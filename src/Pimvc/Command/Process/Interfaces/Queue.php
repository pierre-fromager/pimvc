<?php

/**
 * Description of Queue
 *
 * @author pierrefromager
 */

namespace Pimvc\Command\Process\Interfaces;

use Task as queueTask;

interface Queue
{
    public static function getInstance($queueFilename = '');
    
    public function append(queueTask $task);

    public function run();

    public function remove(queueTask $task);

    public function removeIndex(\int $index);

    public function status(queueTask $task, $status = '');

    public function getQueue();

    public function flush();
}
