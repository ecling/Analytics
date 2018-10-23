<?php

use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Cli\Console as ConsoleApp;
use Phalcon\Loader;
use Phalcon\Db\Adapter\MongoDB\Client;

ini_set('date.timezone','UTC');

$config = include __DIR__ . '/../config/config.php';

// Using the CLI factory default services container
$di = new CliDI();

$di->set('config',$config,true);

$di->set('collectionManager', function () {
    return new \Phalcon\Mvc\Collection\Manager();
}, true);

$di->set(
    'mongo',
    function () {
        $mongo = new Client();

        return $mongo->selectDatabase('test');
    },
    true
);

/**
 * Register the autoloader and tell it to register the tasks directory
 */
$loader = new Loader();

$loader->registerDirs(
        [
            __DIR__ . '/../tasks',
            __DIR__ .'/../apps/models/'
        ]
    )
    ->registerNamespaces([
        'Phalcon' =>  __DIR__.'/../Library/Phalcon/'
    ]);

$loader->register();

// Create a console application
$console = new ConsoleApp();

$console->setDI($di);

/**
 * Process the console arguments
 */
$arguments = [];

foreach ($argv as $k => $arg) {
    if ($k === 1) {
        $arguments['task'] = $arg;
    } elseif ($k === 2) {
        $arguments['action'] = $arg;
    } elseif ($k >= 3) {
        $arguments['params'][] = $arg;
    }
}

try {
    // Handle incoming arguments
    $console->handle($arguments);
} catch (\Phalcon\Exception $e) {
    // Do Phalcon related stuff here
    // ..
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
} catch (\Throwable $throwable) {
    fwrite(STDERR, $throwable->getMessage() . PHP_EOL);
    exit(1);
}