<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/9/24
 * Time: 4:44
 */
use Phalcon\Mvc\MongoCollection;

class Website extends MongoCollection
{
    public function initialize()
    {
        $this->setSource('website');
    }
}