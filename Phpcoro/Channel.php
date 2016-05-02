<?php
/*
 * @author  xuruiqi
 * @date    20160501
 * @copyright   reetsee.com
 */
class Phpcoro_Channel {
    protected $_capacity = 0;
    protected $_queue = NULL;
    public function __construct($capacity = -1) {
        $this->_capacity = $capacity;
        $this->_queue = new SplQueue();
    }

    public function empty() {
        return $this->_queue->isEmpty();
    }

    public function size() {
        return $this->_queue->count();
    }

    public function full() {
        return $this->_capacity >= 0 && $this->size() >= $this->_capacity;
    }

    protected function _enqueue($message) {
        $this->_queue->enqueue($message);
    }

    protected function _dequeue() {
        return $this->_queue->dequeue();
    }

    /**
     * @author  xuruiqi
     * @param
     *      $message    // The content to be written to the channel
     * @return
     *      int // The number of message successfully written
     * @desc    Synchronously write a message to the channel
     */
    public function write($message) {
        if ($this->full()) {
            return 0;
        }

        $this->_enqueue($message);
        return 1;
    }

    /**
     * @author  xuruiqi
     * @return
     *      mixed   // The message being read
     * @desc    Asynchronously read from channel
     */
    public function read() {
        $message = NULL;
        while (TRUE) {
            if (!$this->empty()) {
                $message = $this->_dequeue();
                break;
            }
            $bolStopReading = (yield);
            if ($bolStopReading === TRUE) {
                break;
            }
        }

        (yield cororet($message));
    }
}
