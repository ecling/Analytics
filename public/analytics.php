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
        //test
        $test = Conversation::find();
        //$test->save('{ item: "canvas test", qty: 100, tags: ["cotton"], size: { h: 28, w: 35.5, uom: "cm" } }');
        $array = [
            'item'=> 'canvas',
            'qty'=> 100,
            'tags'=>['cotton'],
            'size'=>[
                'h'=>28,
                'w'=>35.5,
                'uom'=>'cm'
            ]
        ];
        //echo json_encode($array);
        var_dump($test[0]);
        exit();
        //验证站点和域名
        $website_id = $_GET['website_id'];
        $domain = $_GET['domain'];
        $website = Website::findFirst([
            'conditions' => [
                'website_id' => $website_id
            ]
        ]);
        if(!$website){
            echo 'error';
            exit();
        }else{
            if($website->domain != $domain){
                echo 'error';
                exit();
            }
        }

        //处理唯一访客
        $user_id = $_GET['uuid'];
        $visitor = Visitor::findFirst([
            'conditions' => [
                'user_id' => $user_id
            ]
        ]);
        if(!$visitor){
            $visitor = new Visitor();
            $visitor->user_id = $user_id;
            if($visitor->save()==false){
                echo "Umh, We can't store robots right now: \n";

                $messages = $visitor->getMessages();

                foreach ($messages as $message) {
                    echo $message, "\n";
                }
            }
        }

        //处理会话
        if($visitor){
            $sid = $_GET['sid'];
            $conversation = Conversation::findFirst([
                'conditions' => [
                    'sid' => $sid
                ]
            ]);
            if($conversation){
                if(isset($_GET['order'])) {
                    $products = array();
                    $order = new Order();
                    $order->order_id = '';
                    $order->ad = '';
                    $order->subtotal = '';
                    foreach ($products as $id=>$_product) {
                        $order->products[$id]->sku = '';
                        $order->products[$id]->qty = '';
                        $order->products[$id]->price = '';
                    }
                    if($order->save()==false){
                        echo 'error';
                    }
                }else{

                }
            }else{
                $conversation = new Conversation();
                $conversation->user_agent = '';
                $conversation->browser_name = '';
                $conversation->browser_version = '';
                $conversation->browser_lang = '';
                $conversation->browser_date = '';
                $conversation->operate = '';
                $conversation->operate_relase = '';
                $conversation->device_pixel_ratio = '';
                $conversation->resolution = '';
                $conversation->utm_source = '';
                $conversation->utm_medium = '';
                $conversation->utm_campaign = '';
                $conversation->utm_term = '';
                $conversation->utm_content = '';
            }
        }

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