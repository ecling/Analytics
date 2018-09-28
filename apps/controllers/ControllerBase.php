<?php


use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    public function beforeExecuteRoute()
    {
        if($this->session->has('user')){

        }else{
            $this->response->redirect('login');
            return;
        }
    }
}
