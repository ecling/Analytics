<?php

use Phalcon\Di;
use Phalcon\Loader;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Router;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;
use Phalcon\Db\Adapter\Pdo\Mysql as Database;
use Phalcon\Db\Adapter\MongoDB\Client;
use Phalcon\Mvc\Model\Manager as ModelManager;
use Phalcon\Mvc\Model\Metadata\Memory as ModelMetadata;
use Phalcon\Mvc\Url;
use Phalcon\Session\Adapter\Files as Session;

/**
 * Very simple MVC structure
 */

ini_set('date.timezone','UTC');

$loader = new Loader();

$loader->registerDirs(
        [
            "../apps/controllers/",
            "../apps/models/",
        ]
    )
    ->registerNamespaces([
        'Phalcon' => '../Library/Phalcon/',
        'Helper' => '../helper/'
    ]);

$loader->register();

$di = new Di();

$di->set('collectionManager', function () {
    return new \Phalcon\Mvc\Collection\Manager();
}, true);

$di->set(
    'session',
    function(){
        $session = new Session();

        $session->start();

        return $session;
    }
);

// Registering a router
$di->set("router", Router::class);

// Registering a dispatcher
$di->set("dispatcher", MvcDispatcher::class);

// Registering a Http\Response
$di->set("response", Response::class);

// Registering a Http\Request
$di->set("request", Request::class);

$di->set("url",Url::class);

// Registering the view component
$di->set(
    "view",
    function () {
        $view = new View();

        $view->setViewsDir("../apps/views/");

        return $view;
    }
);

/*
$di->set(
    "db",
    function () {
        return new Database(
            [
                "host"     => "localhost",
                "username" => "root",
                "password" => "",
                "dbname"   => "invo",
            ]
        );
    }
);
*/

$di->set(
    'mongo',
    function () {
        $mongo = new Client();

        return $mongo->selectDatabase('test');
    },
    true
);

//Registering the Models-Metadata
$di->set("modelsMetadata", ModelMetadata::class);

//Registering the Models Manager
$di->set("modelsManager", ModelManager::class);

try {
    $application = new Application($di);

    $response = $application->handle();

    echo $response->getContent();
} catch (Exception $e) {
    echo $e->getMessage();
}
