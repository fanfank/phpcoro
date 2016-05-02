<?php
$demo_include_path = dirname(__FILE__) . '/../';
set_include_path(get_include_path() . PATH_SEPARATOR . $demo_include_path);

require_once('phpcoro.php');

//Phpcoro_Log::setLogLevel(Phpcoro_Log::LOG_LEVEL_DEBUG);

function f($cnt) {
    for ($i = 0; $i < $cnt; ++$i) {
        echo "Task:" . (yield getTaskId()) . " in f:" . (yield $i) . "\n";
    }
}

function main() {
    Phpcoro_Scheduler::add(f, 5);
    Phpcoro_Scheduler::add(f, 3);
    
    // Pipe
    $channel = new Phpcoro_Channel(5);
    $done_channel = new Phpcoro_Channel(1);

    Phpcoro_Scheduler::add(function () use ($channel, $done_channel) {
        for ($i = 0; $i < 5; ++$i) {
            echo "Read " . (yield $channel->read()) . " from channel\n";
        }
        $done_channel->write(TRUE);
    });

    $channel->write("123");
    Phpcoro_Scheduler::add(function () use ($channel) {
        for ($i = 0; $i < 10; ++$i) {
            echo "Written " . (yield $channel->write($i)) . " messages, i=$i "
                . "current size " . $channel->size() . "\n";
        }
    });

    (yield $done_channel->read());
    echo "done_channel received message\n";
}

Phpcoro_Scheduler::run(main);
echo "Scheduler finished\n";
