<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/10/7
 * Time: 12:23
 */

namespace Helper;

class Date{
    public function test(){
        echo 22;
    }
    public function getUtcDateTime($date,$format='Y-m-d H:i:s',$option = array()){
        $date = new \DateTime($date, new \DateTimeZone('Asia/Shanghai'));
        if(isset($option['add'])){
            $date->add(new \DateInterval($option['add']));
        }
        $date->setTimezone(new \DateTimeZone('utc'));
        return $date->format($format);
    }

    public function getLocalDateTime($date,$format,$option = array()){
        $date = new \DateTime($date);
        if(isset($option['add'])){
            $date->add(new \DateInterval($option['add']));
        }
        $date->setTimezone(new \DateTimeZone('Asia/Shanghai'));
        return $date->format($format);
    }
}