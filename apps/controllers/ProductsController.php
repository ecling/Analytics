<?php

use Phalcon\Mvc\Controller;

class ProductsController extends Controller
{
    public function indexAction()
    {
        //$this->view->product = Products::findFirst();
        $test = new Ids();
        var_dump($test->getAdId());
        exit();
    }
}
