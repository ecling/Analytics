<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/30 0030
 * Time: 11:36
 */

use Phalcon\Mvc\Controller;
use Helper\Date as FormatDate;

class OrderController extends ControllerBase
{
    public function adAction(){
        $helper = new FormatDate();

        //确认开始结束时间
        if($this->session->has('start_time')){
            $start_time = $this->session->get('start_time');
        }else{
            $start_time = new \DateTime('now');
            $start_time->sub(new \DateInterval('P7D'));
            $start_time = $start_time->format('Y-m-d H:i:s');
        }

        if($this->session->has('end_time')){
            $end_time = $this->session->get('end_time');
        }else{
            $end_time = date('Y-m-d H:i:s',time());
        }

        //浏览量聚合
        $views = Conversation::aggregate([
            [
                '$match' => [
                    'is_system_ad' => 1,
                    'created_at' => [
                        '$gte' => $start_time,
                        '$lte' => $end_time
                    ]
                ],
            ],
            [
                '$group' => [
                    '_id' => '$utm_content',
                    'total'  => [
                        '$sum' => 1,
                    ],
                ]
            ]
        ]);

        $ad_view = array();
        foreach ($views->toArray() as $item) {
            $tem_item = new ArrayIterator($item);
            $ad_view[$tem_item->_id] = $tem_item->total;
        }

        //已付款订单聚合
        $order_paid = Order::aggregate([
            [
                '$match' => [
                    'is_system_ad' => 1,
                    'status' => 1,
                    'created_at' => [
                        '$gte' => $start_time,
                        '$lte' => $end_time
                    ]
                ]
            ],
            [
                '$group' => [
                    '_id' => '$ad_id',
                    'total'  => [
                        '$sum' => '$order_total'
                    ],
                    'num' => [
                        '$sum' => 1
                    ]
                ]
            ]
        ]);

        $ad_paid = array();
        foreach($order_paid->toArray() as $item){
            $tem_item = new ArrayIterator($item);
            $ad_paid[$tem_item->_id]['total'] = $tem_item->total;
            $ad_paid[$tem_item->_id]['num'] = $tem_item->num;
        }

        //未付款聚合
        $order_unpaid = Order::aggregate([
            [
                '$match' => [
                    'is_system_ad' => 1,
                    'status' => [
                        '$gte' => 2
                    ],
                    'created_at' => [
                        '$gte' => $start_time,
                        '$lte' => $end_time
                    ]
                ]
            ],
            [
                '$group' => [
                    '_id' => '$ad_id',
                    'total'  => [
                        '$sum' => '$order_total'
                    ],
                    'num' => [
                        '$sum' => 1
                    ]
                ]
            ]
        ]);

        $ad_unpaid = array();
        foreach($order_unpaid->toArray() as $item){
            $tem_item = new ArrayIterator($item);
            $ad_unpaid[$tem_item->_id]['total'] = $tem_item->total;
            $ad_unpaid[$tem_item->_id]['num'] = $tem_item->num;
        }

        $ad = Advertising::find([
            [
                'status' => 1
            ],
            'sort' => [
                'ad_id' => -1
            ]
        ]);
        $items = array();
        $total = array('views'=>0,'paid_num'=>0,'total'=>0,'unpaid_num'=>0,'unpaid_total'=>0);
        foreach ($ad as $_ad) {
            $items[$_ad->ad_id]['ad_id'] = $_ad->ad_id;
            $items[$_ad->ad_id]['ad_name'] = $_ad->name;
            $items[$_ad->ad_id]['image_url'] = $_ad->image_url;
            $items[$_ad->ad_id]['views'] = (isset($ad_view[$_ad->ad_id]))?$ad_view[$_ad->ad_id]:'0';
            $items[$_ad->ad_id]['paid_num'] = (isset($ad_paid[$_ad->ad_id]['num']))?$ad_paid[$_ad->ad_id]['num']:'0';
            $items[$_ad->ad_id]['total'] = (isset($ad_paid[$_ad->ad_id]['total']))?$ad_paid[$_ad->ad_id]['total']:'0';
            $items[$_ad->ad_id]['unpaid_num'] = (isset($ad_unpaid[$_ad->ad_id]['num']))?$ad_unpaid[$_ad->ad_id]['num']:'0';
            $items[$_ad->ad_id]['unpaid_total'] = (isset($ad_unpaid[$_ad->ad_id]['total']))?$ad_unpaid[$_ad->ad_id]['total']:'0';

            if(isset($ad_view[$_ad->ad_id])){
                $total['views']  += $ad_view[$_ad->ad_id];
            }

            if(isset($ad_paid[$_ad->ad_id]['num'])){
                $total['paid_num'] += $ad_paid[$_ad->ad_id]['num'];
            }

            if(isset($ad_paid[$_ad->ad_id]['total'])){
                $total['total'] += $ad_paid[$_ad->ad_id]['total'];
            }

            if(isset($ad_unpaid[$_ad->ad_id]['num'])){
                $total['unpaid_num'] += $ad_unpaid[$_ad->ad_id]['num'];
            }

            if(isset($ad_unpaid[$_ad->ad_id]['total'])){
                $total['unpaid_total'] += $ad_unpaid[$_ad->ad_id]['total'];
            }
        }

        $this->view->start = $helper->getLocalDateTime($start_time,'m/d/Y');
        $this->view->end = $helper->getLocalDateTime($end_time,'m/d/Y');
        $this->view->total = $total;
        $this->view->items = $items;
    }

    public function listAction(){
        $orders = Order::find([
            [],
            'sort' => [
                'created_at' => -1
            ],
            'limit' => 100
        ]);
        $this->view->orders = $orders;
    }
}