<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/9/29
 * Time: 20:36
 */

use Phalcon\Mvc\MongoCollection;

class Order extends MongoCollection
{
    public function initialize()
    {
        $this->setSource('order');
    }
}