<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/9 0009
 * Time: 10:51
 */

use Phalcon\Mvc\MongoCollection;

class Ids extends MongoCollection
{
    public function initialize()
    {
        $this->setSource('ids');
    }

    public function getAdId(){
        $collection = $this->getConnection()->selectCollection('ids');
        $ad = $collection->findOneAndUpdate(
            ['name'=>"Advertising"],
            ['$inc'=> ['id'=>1]],
            ['returnDocument'=>2]
        );
        return $ad->id;
    }
}