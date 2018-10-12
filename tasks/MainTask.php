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
        $time = date('Y-m-d h:i:s',time());
        $order = Order::findFirst([
            'conditions' => [
                'created_at' => [
                    '$lt' => $time
                ]
            ]
        ]);
        var_dump($order);
    }
}