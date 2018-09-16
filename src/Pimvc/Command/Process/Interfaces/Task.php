<?php

/**
 * Description of Task
 *
 * @author pierrefromager
 */

namespace Pimvc\Command\Process\Interfaces;

interface Task
{
    const TASK_APP = 'app';
    const TASK_TZ = 'timeZone';
    const TASK_ID = 'id';
    const TASK_PID = 'pid';
    const TASK_RUN_METHOD = 'run';
    const TASK_STATUS_RUN = 'run';
    const TASK_STATUS_WAIT = 'wait';
    const TASK_STATUS_FAILED = 'failed';
    const TASK_STATUS_SUCCESS = 'success';
    const TASK_DATETIME_FORMAT = 'Y-m-d H:i:s';
    const TASK_DATE_FORMAT = 'Y-m-d';

    public function run();

    public function status();

    public function result();

    public function getId();

    public function getEndtime();
}
