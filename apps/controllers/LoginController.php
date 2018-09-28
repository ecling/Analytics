<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/9/24
 * Time: 4:48
 */

use Phalcon\Mvc\Controller;

class LoginController extends Controller
{
    public function indexAction()
    {

    }

    public function postAction(){
        $this->session->set('user','test');
        $this->response->redirect('index');
    }
}
