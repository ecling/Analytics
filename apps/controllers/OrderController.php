<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/30 0030
 * Time: 11:36
 */

use Phalcon\Mvc\Controller;

class OrderController extends Controller
{
    public function adAction(){
        $this->view->items = array();
    }

    public function listAction(){
        $this->view->orders = array();
    }
}