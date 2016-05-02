<?php
/*
 * @author  xuruiqi
 * @date    20160501
 * @copyright   reetsee.com
 */
class Phpcoro_Scheduler {
    protected static $_bolInit = FALSE;
    protected static $_arrTaskMap = NULL;
    protected static $_intMaxTaskId = 0;
    protected static $_taskQueue = NULL;

    // Forbid using constructor
    private function __construct() {}

    public static function init() {
        if (self::$_bolInit) {
            return;
        }
        self::$_bolInit    = TRUE;
        self::$_arrTaskMap = array();
        self::$_taskQueue  = new SplQueue();
    }

    /**
     * @author  xuruiqi
     * @params
     *      $callable   // A callable
     *      $param1     // Parameters that will be send
     *                  //  to the callable
     *      $param2
     *      ...
     * @return
     *      int // If success == 0 else < 0
     *
     * @example
     *      $res = $container->add(task_func, 1, 2, 3)
     *
     * @desc    Add a task to container
     */
    public static function add(callable $callable) {
        $intArgNum = func_num_args();
        $arrArgList = func_get_args();
        if ($intArgNum < 1) {
            return -1;
        }

        $res = 0;
        if ($callable instanceof Generator) {
            $res = self::addGenerator($callable);

        } else if (is_callable($callable) || is_string($callable)) {
            $generator = call_user_func_array(
                $callable, 
                array_slice($arrArgList, 1)
            );

            $res = self::addGenerator($generator);

        } else {
            Phpcoro_Log::error(
                "The first parameter is not a callable " .
                "or a generator");
            $res = -3;
        }

        return $res;
    }

    public static function addGenerator() {
        $intArgNum = func_num_args();
        $arrArgList = func_get_args();

        if ($intArgNum < 1 || $intArgNum >= 3) {
            Phpcoro_Log::error("Invalid number of input parameters.");
            return -1;
        }
        if (!($arrArgList[0] instanceof Generator)) {
            Phpcoro_Log::error("The first param is not a generator.");
            return -2;
        }
        if ($intArgNum == 2 && 
                !($arrArgList[1] instanceof Phpcoro_Task_Abstract)) {
            Phpcoro_Log::error(
                "The second param is not an instance of " . 
                "Phpcoro_Task_Abstract."
            );
            return -3;
        }

        $task = new Phpcoro_Task_Default($arrArgList[0]);
        if ($intArgNum == 2) { 
            $task->setParent($arrArgList[1]);
        }
        
        $task->setTaskId(self::newTaskId());
        self::_addTask($task->getTaskId(), $task);

        Phpcoro_Log::debug("Add task:" . $task->getTaskId());

        return 0;
    }

    public static function newTaskId() {
        return ++self::$_intMaxTaskId;
    }

    protected static function _addTask($intTaskId, $task) {
        self::$_arrTaskMap[$intTaskId] = $task;
        self::$_taskQueue->enqueue($task);
    }

    protected static function _popTask() {
        return self::$_taskQueue->dequeue();
    }

    protected static function _pushTask($task) {
        self::$_taskQueue->enqueue($task);
    }

    protected static function _removeFromTaskMap($intTaskId) {
        unset(self::$_arrTaskMap[$intTaskId]);
    }

    public static function run() {
        $intArgNum = func_num_args();
        $arrArgList = func_get_args();
        if ($intArgNum > 0) {
            call_user_func_array(
                array(Phpcoro_Scheduler, "add"),
                $arrArgList
            );
        }
        
        while (!self::$_taskQueue->isEmpty()) {
            $task = self::_popTask();
            $retval = $task->run();

            if ($task->isDone()) {
                self::_removeFromTaskMap($task->getTaskId());

                // Find the parent task
                $parentTask = $task->parent();

                if ($parentTask instanceof Phpcoro_Task_Abstract) {
                    // Scheduler parent
                    self::_pushTask($parentTask);
                } else {
                    // Is just a return value or has no parent
                }

                Phpcoro_Log::debug("Task:" . $task->getTaskId() . " done.");

            } else {
                if ($retval instanceof Phpcoro_Syscall) {
                    $syscallRetval = $retval($task);
                    if ($syscallRetval->getNeedSchedule()) {
                        self::_pushTask($task);
                        $task->setSendValue($syscallRetval->getSendValue());
                    }

                } else if ($retval instanceof Phpcoro_Retval) {
                    self::_removeFromTaskMap($task->getTaskId());

                    // Find the parent routine
                    $parentTask = $task->parent();
                    $parentTask->setSendValue($retval->getRetval());

                    // Schedule parent
                    self::_pushTask($parentTask);

                } else if ($retval instanceof Generator) {
                    // A new child task
                    $res = self::addGenerator($retval, $task);
                    if ($res === FALSE) {
                        Phpcoro_Log::error(
                            "Add child task of parent:" . 
                            $task->getTaskId() . 
                            " failed"
                        );
                        $task->setSendValue(NULL);
                    }

                } else {
                    // Other system call or value
                    // TODO
                    $task->setSendValue($retval);
                    self::_pushTask($task);
                }
            }
        }
    }
}
Phpcoro_Scheduler::init();
