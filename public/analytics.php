<?php

error_reporting(E_ALL);

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\DI\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as Database;
use Phalcon\Db\Adapter\MongoDB\Client;
use Phalcon\Paginator\Pager;

try {

    /**
     * Read the configuration
     */
    $config = include __DIR__ . '/../config/config.php';

    /**
     * Registering an autoloader
     */
    $loader = new Loader();

    $loader
        ->registerDirs([$config->application->modelsDir])
        ->registerNamespaces([
            'Phalcon' => '../Library/Phalcon/'
        ])
        ->register();

    $di = new FactoryDefault();

    /**
     * The URL component is used to generate all kind of urls in the application
     */
    $di->set('url', function () use ($config) {
        $url = new \Phalcon\Mvc\Url();
        $url->setBaseUri($config->application->baseUri);

        return $url;
    });

    $di->set('collectionManager', function () {
        return new \Phalcon\Mvc\Collection\Manager();
    }, true);

    /**
     * Database connection is created based in the parameters defined in the configuration file
     */
    /*
    $di->set('db', function () use ($config) {
        return new Database(
            [
                "host"     => $config->database->host,
                "username" => $config->database->username,
                "password" => $config->database->password,
                "dbname"   => $config->database->name
            ]
        );
    });
    */
    $di->set(
        'mongo',
        function () {
            $mongo = new Client();

            return $mongo->selectDatabase('test');
        },
        true
    );

    /**
     * Starting the application
     */
    $app = new Micro();

    /**
     * Add your routes here
     */
    $app->get('/', function () {
        //$pare = $_GET;
        //$user_id = $_GET['uid'];
        $test = Conversation::find(
            [
                'conditions' => [
                    'size.h' => 28
                ]
            ]
        );
        //echo 'There are ', count($test), "\n";
        var_dump($test);
        /*
        echo json_encode(
            [
                'code' => 200,
                'name' => 'test',
            ]
        );
        */
    });

    /**
     * Not found handler
     */
    $app->notFound(function () use ($app) {
        //$app->response->setStatusCode(404, "Not Found")->sendHeaders();
        //require __DIR__ . "/../views/404.phtml";
    });

    /**
     * Handle the request
     */
    $app->handle();
} catch (\Exception $e) {
    echo $e->getMessage(), PHP_EOL;
    echo $e->getTraceAsString();
}