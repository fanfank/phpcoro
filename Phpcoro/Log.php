<?php
/*
 * @author  xuruiqi
 * @date    20160501
 * @copyright   reetsee.com
 */
class Phpcoro_Log {
    const LOG_LEVEL_DEBUG    = 1;
    const LOG_LEVEL_INFO     = 2;
    const LOG_LEVEL_WARNING  = 3;
    const LOG_LEVEL_ERROR    = 4;
    const LOG_LEVEL_CRITICAL = 5;

    protected static $_intLogLevel = 2;
    protected static $_logHandler  = STDOUT;

    private function __construct() {}

    public static function setLogLevel($intLogLevel) {
        self::$_intLogLevel = $intLogLevel;
    }
    public static function getLogLevel() {
        self::$_intLogLevel;
    }

    public static function debug($strMsg, $intTraceLevel = 0) {
        self::log(self::LOG_LEVEL_DEBUG, $strMsg, $intTraceLevel);
    }

    public static function info($strMsg, $intTraceLevel = 0) {
        self::log(self::LOG_LEVEL_INFO, $strMsg, $intTraceLevel);
    }

    public static function notice($strMsg, $intTraceLevel = 0) {
        self::info($strMsg, $intTraceLevel + 1);
    }

    public static function warning($strMsg, $intTraceLevel = 0) {
        self::log(self::LOG_LEVEL_WARNING, $strMsg, $intTraceLevel);
    }

    public static function warn($strMsg, $intTraceLevel = 0) {
        self::warning($strMsg, $intTraceLevel + 1);
    }

    public static function error($strMsg, $intTraceLevel = 0) {
        self::log(self::LOG_LEVEL_ERROR, $strMsg, $intTraceLevel);
    }

    public static function critical($strMsg, $intTraceLevel = 0) {
        self::log(self::LOG_LEVEL_CRITICAL, $strMsg, $intTraceLevel);
    }

    public static function fatal($strMsg, $intTraceLevel = 0) {
        self::critical($strMsg, $intTraceLevel + 1);
    }
    
    public static function log($intLogLevel, $strMsg, $intTraceLevel = 0) {
        if (self::$_intLogLevel > $intLogLevel) {
            return;
        }

        switch($intLogLevel) {
        case self::LOG_LEVEL_CRITICAL:
            $strPrepend = "FATAL";
            $strAppend  = "\n";
            break;
        case self::LOG_LEVEL_ERROR:
            $strPrepend = "ERROR";
            $strAppend  = "\n";
            break;
        case self::LOG_LEVEL_WARNING:
            $strPrepend = "WARNING";
            $strAppend  = "\n";
            break;
        case self::LOG_LEVEL_INFO:
            $strPrepend = "INFO";
            $strAppend  = "\n";
            break;
        case self::LOG_LEVEL_DEBUG:
            $strPrepend = "DEBUG";
            $strAppend  = "\n";
            break;
        }

        $arrTrace = debug_backtrace();
        $intDepth = 1 + $intTraceLevel;
        $intTraceDepth = count($arrTrace);
        if ($intDepth > $intTraceDepth) {
            $intDepth = $intTraceDepth;
        }
        $arrTargetTrace = $arrTrace[$intDepth];
        unset($arrTrace);
        if (!isset($arrTargetTrace['file'])) {
            $arrTargetTrace['file'] = "UnknownFile";
        }

        $strPrepend = $strPrepend .
            " " . strval(@date("Y-m-d H:i:s")) .
            " {$arrTargetTrace['file']}:{$arrTargetTrace['line']}";
            //" {$arrTargetTrace['class']}::{$arrTargetTrace['function']}";

        $strMsg = "$strPrepend $strMsg $strAppend";

        self::_writeLog($strMsg);
    }

    public static function setLogHandler($handler = STDOUT) {
        self::$_logHandler = $handler;
    }
    public static function getLogHandler() {
        return self::$_logHandler;
    }
    public static function resetLogHandler() {
        self::setLogHandler(STDOUT);
    }
    protected static function _writeLog($strMsg) {
        $handler = self::getLogHandler();
        fwrite($handler, $strMsg);
    }
}
