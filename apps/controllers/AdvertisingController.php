<?php

use Phalcon\Mvc\Controller;

class ProductsController extends Controller
{
    public function indexAction()
    {
        $this->view->product = Products::findFirst();
    }

    public function addAction(){

    }

    public  function listAction(){
        $this->view->Adlist = Advertising::find();
    }
}
