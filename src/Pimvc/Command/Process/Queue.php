<?php

/**
 * Description of Pimvc\Commands\Process\Queue
 *
 * @author pierrefromager
 */

namespace Pimvc\Command\Process;

use Pimvc\Command\Process\Interfaces\Task as queueTask;

class Queue implements Interfaces\Queue
{
    const DEFAULT_QUEUE_FILENAME = 'queue.json';
    const QUEUE_CACHE = 'cache';
    const QUEUE_ORDER_METHOD = 'queueReorder';

    private static $instance;

    private $app;
    private $queueFilename;
    private $queue = [];

    /**
     * getInstance
     *
     * @param string $queueFilename
     * @return Queue
     */
    public static function getInstance($queueFilename = '')
    {
        if (!self::$instance) {
            self::$instance = new self($queueFilename);
        }
        return self::$instance;
    }

    /**
     * getQueue
     *
     * @return string
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * process
     *
     * @return $this
     */
    public function run()
    {
        if (($task = $this->getNext()) !== false) {
            $this->runTask($task);
        }
        return $this;
    }

    /**
     * status
     *
     * @param queueTask $task
     * @param string $status
     * @return string
     */
    public function status(queueTask $task, $status = '')
    {
        return $task->status($status);
    }

    /**
     * append
     *
     * @param queueTask $task
     * @return $this
     */
    public function append(queueTask $task)
    {
        if ($this->taskExist($task)) {
            return $this;
        }
        $this->queue[] = $task;
        $this->save();
        return $this;
    }
    
    /**
     * remove
     *
     * @param queueTask $task
     * @return $this
     */
    public function remove(queueTask $task)
    {
        $queueSize = count($this->queue);
        for ($c = 0; $c < $queueSize; ++$c) {
            if ($this->queue[$c]->id === $task->id) {
                unset($this->queue[$c]);
            }
        }
        $this->save();
        return $this;
    }
    
    /**
     * removeIndex
     *
     * @param int $index
     * @return $this
     */
    public function removeIndex(\int $index)
    {
        if (isset($this->queue[$index])) {
            unset($this->queue[$index]);
            $this->save();
        }
        return $this;
    }

    /**
     * flush
     *
     */
    public function flush()
    {
        $this->queue = [];
        $this->save();
    }
    
    /**
     * taskExist
     *
     * @param queueTask $task
     * @return boolean
     */
    private function taskExist(queueTask $task)
    {
        $queueSize = count($this->queue);
        for ($c = 0; $c < $queueSize; ++$c) {
            if ($this->queue[$c]->getId() === $task->getId()) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * __construct
     *
     * @param string $queueFilename
     */
    private function __construct($queueFilename = '')
    {
        $this->app = \Pimvc\App::getInstance();
        $this->queueFilename = ($queueFilename)
            ? $queueFilename
            : $this->queueFilename();
        $this->load();
    }

    /**
     * getNext
     *
     * @return false | queueTask
     */
    private function getNext()
    {
        uasort($this->queue, [$this, self::QUEUE_ORDER_METHOD]);
        $queueSize = count($this->queue);
        for ($c = 0; $c < $queueSize; $c++) {
            $task = $this->queue[$c];
            if ($task->status() === queueTask::TASK_STATUS_WAIT) {
                return $task;
            }
        }
        return false;
    }

    /**
     * update
     *
     * @param queueTask $task
     * @return $this
     */
    private function update(queueTask $task)
    {
        $queueSize = count($this->queue);
        for ($c = 0; $c < $queueSize; ++$c) {
            if ($this->queue[$c]->getId() === $task->getId()) {
                $this->queue[$c] = $task;
                $this->save();
            }
        }
        return $this;
    }
    
    /**
     * queueFilename
     *
     * @return string
     */
    private function queueFilename()
    {
        $filename = ($this->queueFilename)
            ? $this->queueFilename
            : self::DEFAULT_QUEUE_FILENAME;
        return $this->app->getPath() . self::QUEUE_CACHE . DIRECTORY_SEPARATOR
            . $filename;
    }
    
    /**
     * queueReorder
     *
     * @param queueTask $a
     * @param queueTask $b
     * @return int
     */
    private function queueReorder(queueTask $a, queueTask $b)
    {
        $eta = $a->getEndtime();
        $etb = $b->getEndtime();
        if ($eta == $etb) {
            return 0;
        } elseif ($eta > $etb) {
            return -1;
        } else {
            return 1;
        }
    }
    
    /**
     * runTask
     *
     * @param queueTask $task
     */
    private function runTask(queueTask $task)
    {
        if ($task !== false) {
            $task->run();
            $this->update($task);
        }
        return $this;
    }
    
    /**
     * save
     *
     */
    private function save()
    {
        file_put_contents($this->queueFilename, serialize($this->queue));
    }

    /**
     * load
     *
     */
    private function load()
    {
        if (file_exists($this->queueFilename)) {
            $this->queue = unserialize(file_get_contents($this->queueFilename));
        }
    }
}
