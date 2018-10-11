<?php

error_reporting(E_ALL);

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\DI\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as Database;
use Phalcon\Db\Adapter\MongoDB\Client;
use Phalcon\Paginator\Pager;

ini_set('date.timezone','UTC');

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
        //$test = Conversation::find();
        //$test->save('{ item: "canvas test", qty: 100, tags: ["cotton"], size: { h: 28, w: 35.5, uom: "cm" } }');
        /*
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
        */
        //echo json_encode($array);
        //var_dump((string)$test[0]->getId());
        //exit();
        //验证站点和域名

        $time = date('Y-m-d H:i:s',time());

        //get ip
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $website_id = $_GET['website_id'];
        $domain = $_GET['domain'];
        $website = Website::findById($website_id);
        if(!$website){
            echo 'website does not exist';
            exit();
        }else{
            if($website->domain != $domain){
                echo 'Domain Mismatch';
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
            $visitor->website_id = $website_id;
            $visitor->created_at = $time;
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

            //判断是否属于系统广告
            $is_system_ad = 0;
            if(isset($_GET['utm_content'])){
                $ad = Advertising::findFirst([
                    [
                        'ad_id' => (float)$_GET['utm_content']
                    ]
                ]);
                if($ad){
                    $is_system_ad = 1;
                }else{
                    $is_system_ad = 0;
                }
            }

            if($conversation){

            }else{
                $conversation = new Conversation();
                $conversation->sid = $sid;
                $conversation->website_id = $website_id;
                $conversation->user_agent = (isset($_GET['user_agent']))?$_GET['user_agent']:'';
                $conversation->browser_name = (isset($_GET['browser_name']))?$_GET['browser_name']:'';
                $conversation->browser_version = (isset($_GET['browser_version']))?$_GET['browser_version']:'';
                $conversation->browser_lang = (isset($_GET['browser_lang']))?$_GET['browser_lang']:'';
                $conversation->browser_date = $time;
                $conversation->operate = (isset($_GET['operate']))?$_GET['operate']:'';
                $conversation->operate_relase = (isset($_GET['operate_relase']))?$_GET['operate_relase']:'';
                $conversation->device_pixel_ratio = (isset($_GET['device_pixel_ratio']))?$_GET['device_pixel_ratio']:'';
                $conversation->resolution = (isset($_GET['resolution']))?$_GET['resolution']:'';
                $conversation->color_depth = (isset($_GET['color_depth']))?$_GET['color_depth']:'';
                $conversation->utm_source = (isset($_GET['utm_source']))?$_GET['utm_source']:'';
                $conversation->utm_medium = (isset($_GET['utm_medium']))?$_GET['utm_medium']:'';
                $conversation->utm_campaign = (isset($_GET['utm_campaign']))?$_GET['utm_campaign']:'';
                $conversation->utm_term = (isset($_GET['utm_term']))?$_GET['utm_term']:'';
                $conversation->utm_content = (isset($_GET['utm_content']))?$_GET['utm_content']:'';
                $conversation->is_system_ad = $is_system_ad;
                $conversation->ip = $ip;
                $conversation->created_at = $time;
                if($conversation->save() === false){
                    echo 'ad save fail';
                }
            }

            if(isset($_GET['order_id'])&&isset($_GET['order_total'])) {
                $order = Order::findFirst([
                    [
                        'order_id' => $_GET['order_id']
                    ]
                ]);
                if(!$order) {
                    $order = new Order();
                    $order->order_id = $_GET['order_id'];
                    $order->order_total = $_GET['order_total'];
                    $order->sid = (isset($_GET['sid']))?$_GET['sid']:'';
                    $order->website_id = $website_id;
                    $order->ad_id = (isset($_GET['utm_content']))?$_GET['utm_content']:'';
                    $order->is_system_ad = $is_system_ad;
                    $order->ip  = $ip;
                    $order->created_at = $time;
                    $order->status = 2;
                    if ($order->save() == false) {
                        echo 'order save fail';
                    }
                }else{
                    echo 'order already exists';
                }
            }else{

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