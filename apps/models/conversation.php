<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/9/24
 * Time: 4:35
 */

use Phalcon\Mvc\MongoCollection;

class Conversation extends MongoCollection
{
    public function initialize()
    {
        $this->setSource('inventory');
    }
}