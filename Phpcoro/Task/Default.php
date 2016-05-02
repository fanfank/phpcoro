<?php
/*
 * @author  xuruiqi
 * @date    20160501
 * @copyright   reetsee.com
 */
class Phpcoro_Task_Default extends Phpcoro_Task_Abstract {
    protected $_bolFirstRun = NULL;
    protected $_callable = NULL;
    protected $_arrParamList = NULL;
    protected $_generator = NULL;
    protected $_sendValue = NULL;

    /**
     * @author  xuruiqi
     * @params
     *      Option 1:
     *          $callable   // A callable which will become a generator
     *          $param1     // Parameters that will be send to the
     *                      //  callable
     *          $param2
     *          ...
     *      Option 2:
     *          $generator  // A generator
     *
     */
    public function __construct() {
        $intArgNum = func_num_args();
        $arrArgList = func_get_args();

        if ($intArgNum == 0) {
            throw new Exception("No parameters found in constructor");
        }

        if (is_string($arrArgList[0]) || is_callable($arrArgList[0])) {
            $this->_initByCallable(
                $arrArgList[0], 
                $arrArgList[1]
            );

        } else if ($arrArgList[0] instanceof Generator) {
            $this->_initByGenerator(
                $arrArgList[0]
            );

        } else {
            throw new Exception("No callable or generator found");
        }

        $this->setParent(NULL);
    }

    protected function _initByCallable($callable, $arrParamList) {
        $this->_bolFirstRun = TRUE;
        $this->_callable = $callable;
        $this->_arrParamList = $arrParamList;
    }

    protected function _initByGenerator($generator) {
        $this->_bolFirstRun = TRUE;
        $this->_generator = $generator;
    }

    public function setSendValue($sendValue) {
        $this->_sendValue = $sendValue;
    }

    public function run() {
        if ($this->_bolFirstRun) {
            $this->_bolFirstRun = FALSE;
            if ($this->_generator === NULL) {
                $this->_generator = call_user_func_array(
                    $this->_callable, 
                    $this->_arrParamList
                );
            }
            $retval = $this->_generator->current();
        } else {
            $retval = $this->_generator->send($this->_sendValue);
            $this->_sendValue = NULL;
        }

        return $retval;
    }

    public function isDone() {
        return !$this->_generator->valid();
    }
}
