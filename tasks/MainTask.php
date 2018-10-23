<?php

use Phalcon\Cli\Task;

class MainTask extends Task
{
    public function mainAction()
    {
        echo 'This is the default task and the default action' . PHP_EOL;
    }

    public function orderAction(array $params){
        /*
        echo sprintf('hello %s', $params[0]);

        echo PHP_EOL;

        echo sprintf('best regards, %s', $params[1]);

        echo PHP_EOL;
        */
        //查询两个小时之前的订单
        $time = date('Y-m-d h:i:s',time()-2*3600);
        $order = Order::find([
            'conditions' => [
                'created_at' => [
                    '$lt' => $time
                ],
                'status' => [
                    '$eq' => 2
                ]
            ],
            'limit' => 10
        ]);

        $client = new SoapClient('https://www.bellecat.com/api/soap/?wsdl');

        // If somestuff requires API authentication,
        // then get a session token
        $session = $client->login('analytics', 'ssI3wz%CZb5ZHfJ7kk*h3anp7Luu1UCz');

        foreach ($order as $_order) {
            $result = $client->call($session, 'sales_order.info', $_order->order_id);

            if(isset($result['state'])&&($result['state'] == 'processing')||$result['state'] == 'complete'){
                $_order->status = 1;
            }else{
                $_order->status = 3;
            }

            if($_order->save() === false){

            }
        }

        // If you don't need the session anymore
        $client->endSession($session);
    }
}