<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/30 0030
 * Time: 11:36
 */

use Phalcon\Mvc\Controller;

class OrderController extends ControllerBase
{
    public function adAction(){
        //浏览量聚合
        $views = Conversation::aggregate([
            [
                '$match' => [
                    'is_system_ad' => 1,
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
                    'status' => 1
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
        foreach ($ad as $_ad) {
            $items[$_ad->ad_id]['ad_id'] = $_ad->ad_id;
            $items[$_ad->ad_id]['ad_name'] = $_ad->name;
            $items[$_ad->ad_id]['image_url'] = $_ad->image_url;
            $items[$_ad->ad_id]['views'] = (isset($ad_view[$_ad->ad_id]))?$ad_view[$_ad->ad_id]:'0';
            $items[$_ad->ad_id]['paid_num'] = (isset($ad_paid[$_ad->ad_id]['num']))?$ad_paid[$_ad->ad_id]['num']:'0';
            $items[$_ad->ad_id]['total'] = (isset($ad_paid[$_ad->ad_id]['total']))?$ad_paid[$_ad->ad_id]['total']:'0';
            $items[$_ad->ad_id]['unpaid_num'] = (isset($ad_unpaid[$_ad->ad_id]['num']))?$ad_unpaid[$_ad->ad_id]['num']:'0';
            $items[$_ad->ad_id]['unpaid_total'] = (isset($ad_unpaid[$_ad->ad_id]['total']))?$ad_unpaid[$_ad->ad_id]['total']:'0';
        }

        $this->view->items = $items;
    }

    public function listAction(){
        $orders = Order::find();
        $this->view->orders = $orders;
    }
}