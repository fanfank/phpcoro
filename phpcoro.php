<?php
// Do php version check
$arrPhpVersion = explode(".", phpversion());
if (intval($arrPhpVersion[0]) < 5 || 
        (intval($arrPhpVersion[0] == 5) && intval($arrPhpVersion[1] < 5))) {
    fwrite(STDERR, "Php version should be newer than 5.5. Current version:" 
            . phpversion() . "\n");
    exit(-255);
}

define('PHPCORO_PATH', dirname(__FILE__));
function __phpcoro_autoload($strClassName) {
    if (substr($strClassName, 0, strlen('Phpcoro_')) === 'Phpcoro_') {
        require_once PHPCORO_PATH . '/' . str_replace('_', '/', $strClassName) . '.php';
    }
}
spl_autoload_register('__phpcoro_autoload');

function cororet($retval) {
    return new Phpcoro_Retval($retval);
}

function getTaskId() {
    return new Phpcoro_Syscall(
        function (Phpcoro_Task_Abstract $task) {
            return new Phpcoro_Syscall_Retval(TRUE, $task->getTaskId());
        }
    );
}
