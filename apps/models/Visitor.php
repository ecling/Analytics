<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/9/29
 * Time: 20:37
 */
use Phalcon\Mvc\MongoCollection;

class Visitor extends MongoCollection
{
    public function initialize()
    {
        $this->setSource('visitor');
    }
}