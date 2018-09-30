<?php


use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    public function beforeExecuteRoute()
    {
        //测试去除登录验证
        /*
        if($this->session->has('user')){

        }else{
            $this->response->redirect('login');
            return;
        }
        */
    }
}
