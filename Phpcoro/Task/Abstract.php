<?php
/*
 * @author  xuruiqi
 * @date    20160501
 * @copyright   reetsee.com
 */
abstract class Phpcoro_Task_Abstract {
    protected $_intTaskId = -1;
    protected $_generator = NULL;
    protected $_parent = NULL;

    public function setTaskId($intTaskId) {
        $this->_intTaskId = $intTaskId;
    }

    public function getTaskId() {
        return $this->_intTaskId;
    }

    public function setParent($parent) {
        $this->_parent = $parent;
    }

    public function getParent() {
        return $this->_parent;
    }

    public function parent() {
        return $this->getParent();
    }

    abstract function run();
}
