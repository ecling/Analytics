<?php

use Phalcon\Cli\Task;

class MainTask extends Task
{
    public function mainAction()
    {
        echo 'This is the default task and the default action' . PHP_EOL;
    }

    /**
     * @param array $params
     * 判断订单状态
     */
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
        $session = $client->login($this->config->api->api_user, $this->config->api->api_key);

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

    /**
     * @param array $params
     * 判断订单广告来源
     */
    public function adAction(array $params){
        $time = date('Y-m-d h:i:s',time());
        //查找广告来源为空的订单
        $order = Order::find([
            'conditions' => [
                'created_at' => [
                    '$lt' => $time
                ],
                'ad_id' => '',
            ],
            'sort' => [
                'created_at' => -1
            ],
            'limit' => 10
        ]);

        foreach ($order as $_order) {
            $created_at = $_order->created_at;
            $start_at = date('Y-m-d H:i:s',strtotime($created_at)-30*24*3600);

            //查找订单对应的会话
            $conversation = Conversation::findFirst([
                [
                    'sid' => $_order->sid
                ]
            ]);
            //echo $_order->order_id."\n";
            $ad_id = null;
            if($conversation){
                //根据IP查找该IP所有的会话
                $colleciton = Conversation::find([
                    'conditions' => [
                        'ip' => $conversation->ip,
                        'created_at' => [
                            '$lt' => $created_at,
                            '$gt' => $start_at
                        ]
                    ],
                    'sort' => [
                        'created_at' => -1
                    ]
                ]);
                foreach ($colleciton as $_item){
                    if(!empty($_item->utm_content)){
                        $ad_id = $_item->utm_content;
                        break;
                    }
                }
            }
            if($ad_id){
                //判断是否为系统广告
                $ad = Advertising::findFirst([
                    [
                        'ad_id' => (float)$ad_id
                    ]
                ]);
                if($ad){
                    $_order->is_system_ad = 1;
                    //echo '1--';
                }else{
                    $_order->is_system_ad = 0;
                    //echo '0--';
                }
                $_order->ad_id = (string)$ad_id;
                //echo $ad_id."\n";
            }else{
                $_order->ad_id = 'direct';
                //echo 'direct'."\n";
            }
            if($_order->save() === false){

            }
        }
    }
}