<?php
$demo_include_path = dirname(__FILE__) . '/../';
set_include_path(get_include_path() . PATH_SEPARATOR . $demo_include_path);

require_once('phpcoro.php');

function level1() {
    echo "Enter level1\n";
    $task_id = (yield getTaskId());
    for ($i = 0; $i < 5; ++$i) {
        echo "Task:$task_id" . " in level1, i=$i\n";
        yield;
        if ($task_id % 2 && $i == 0) {
            (yield level2());
        }
    }
}

function level2() {
    echo "Enter level2\n";
    $task_id = (yield getTaskId());
    for ($i = 0; $i < 3; ++$i) {
        echo "Task:$task_id" . " in level2, i=$i\n";
        yield;
    }
}

Phpcoro_Scheduler::add(level1);
Phpcoro_Scheduler::add(level1);
Phpcoro_Scheduler::run();
