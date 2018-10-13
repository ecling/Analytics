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
            $items[$_ad->ad_id]['views'] = 0;
            $items[$_ad->ad_id]['total'] = 0;
            $items[$_ad->ad_id]['unpaid_total'] = 0;
        }

        $this->view->items = $items;
    }

    public function listAction(){
        $order  = Order::find();
        $this->view->orders = $order;
    }
}