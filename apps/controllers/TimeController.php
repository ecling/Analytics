<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/13 0013
 * Time: 10:31
 */

use Phalcon\Mvc\Controller;
use Helper\Date as FormatDate;

class TimeController extends ControllerBase{
    public function ajaxAction(){
        $helper = new FormatDate();
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        if(isset($start_time)){
            $start_time = $helper->getUtcDateTime($start_time);
            $this->session->set('start_time',$start_time);
        }

        if(isset($end_time)){
            $end_time = $helper->getUtcDateTime($end_time,'Y-m-d H:i:s',['add'=>'PT23H59M59S']);
            $this->session->set('end_time',$end_time);
        }
    }
}