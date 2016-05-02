<?php
$demo_include_path = dirname(__FILE__) . '/../';
set_include_path(get_include_path() . PATH_SEPARATOR . $demo_include_path);

require_once('phpcoro.php');

Phpcoro_Log::setLogLevel(Phpcoro_Log::LOG_LEVEL_DEBUG);

function task1($num) {
    echo "task1 num is $num\n";
    for ($i = 0; $i < $num; ++$i) {
        echo "task1 says $i\n";
        yield;
    }
}

function task2($num) {
    echo "task2 num is $num\n";
    for ($i = 0; $i < $num; ++$i) {
        echo "task2 says $i\n";
        yield;
    }
}

Phpcoro_Scheduler::add(task1, 4);
Phpcoro_Scheduler::add(task2, 10);
Phpcoro_Scheduler::run();

echo "Finished\n";
