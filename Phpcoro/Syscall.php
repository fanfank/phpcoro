<?php
/*
 * @author  xuruiqi
 * @date    20160502
 * @copyright   reetsee.com
 */
class Phpcoro_Syscall {
    protected $_callback;
    public function __construct(callable $_callback) {
        $this->_callback = $_callback;
    }

    public function __invoke(Phpcoro_Task_Abstract $task) {
        $callback = $this->_callback;
        return $callback($task);
    }
};
