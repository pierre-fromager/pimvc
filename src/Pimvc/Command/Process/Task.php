<?php

/**
 * Description of Pimvc\Commands\Process\Task
 *
 * @author pierrefromager
 */

namespace Pimvc\Command\Process;

class Task implements Interfaces\Task
{
    protected $status;
    protected $classname;
    protected $arguments;
    
    protected $id;
    protected $pid;
    protected $errorCode;
    protected $errorMessage;
    protected $startime;
    protected $endtime;
    protected $result;

    /**
     * __construct
     *
     * @param string $classname
     * @param string $arguments
     * @return $this
     */
    public function __construct($classname, $arguments = [])
    {
        $this->classname = $classname;
        $this->arguments = $arguments;
        $this->setId();
        $this->status(self::TASK_STATUS_WAIT);
        return $this;
    }

    /**
     * run
     */
    public function run()
    {
        $this->status(self::TASK_STATUS_RUN);
        $this->startime = $this->now();
        $command = [new $this->classname, self::TASK_RUN_METHOD];
        $args = ($this->arguments) ? $this->arguments : [];
        $this->status(self::TASK_STATUS_RUN);
        try {
            $this->setPid();
            $this->result = call_user_func_array($command, $args);
            $this->status(self::TASK_STATUS_SUCCESS);
        } catch (\Exception $e) {
            $this->status(self::TASK_STATUS_FAILED);
            $this->errorCode = $e->getCode();
            $this->errorMessage = $e->getMessage();
        }
        unset($command);
        $this->endtime = $this->now();
        return $this;
    }

    /**
     * status
     *
     * @param string $status
     * @return string
     */
    public function status($status = '')
    {
        if ($status) {
            $this->status = $status;
        }
        return $this->status;
    }

    /**
     * result
     *
     * @return mixed
     */
    public function result()
    {
        return $this->result;
    }
    
    /**
     * getId
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
    
    
    /**
     * getEndtime
     *
     * @return float
     */
    public function getEndtime()
    {
        return (float) $this->endtime;
    }

    /**
     * now
     *
     * @return string
     */
    private function now()
    {
        return microtime(true);
    }

    /**
     * setId
     *
     */
    private function setId()
    {
        $this->id = md5($this->classname . serialize($this->arguments));
    }

    /**
     * setPid
     *
     */
    private function setPid()
    {
        $this->pid = getmypid();
    }
}
