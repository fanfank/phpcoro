<?php
/*
 * @author  xuruiqi
 * @date    20160502
 * @copyright   reetsee.com
 */
class Phpcoro_Syscall_Retval {
    protected $_bolNeedSchedule = NULL;
    protected $_sendValue = NULL;
    public function __construct($bolNeedSchedule, $sendValue = NULL) {
        $this->_bolNeedSchedule = $bolNeedSchedule;
        $this->_sendValue = $sendValue;
    }
    public function getNeedSchedule() {
        return $this->_bolNeedSchedule;
    }
    public function getSendValue() {
        return $this->_sendValue;
    }
};
