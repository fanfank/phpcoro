<?php
/*
 * @author  xuruiqi
 * @date    20160501
 * @copyright   reetsee.com
 */
class Phpcoro_Retval {
    protected $_retval = NULL;

    public function __construct($retval) {
        $this->setRetval($retval);
    }
    public function getRetval() {
        return $this->_retval;
    }
    public function setRetval($retval) {
        $this->_retval = $retval;
    }
}
