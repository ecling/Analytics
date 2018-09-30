<?php

use Phalcon\Mvc\Controller;

class AdvertisingController extends Controller
{
    public function indexAction()
    {
        $this->view->product = Products::findFirst();
    }

    public function editAction(){

    }

    public function saveAction(){

    }

    public  function listAction(){
        
    }
}
