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
        $postData  = $_POST;
        if(isset($_POST['email'])&&$_POST['password']){
            $user = User::findFirst([
                'conditions' => [
                    'email' => $postData['email']
                ]
            ]);
            if($user){
                if($user->password==md5($_POST['password'])){
                    $this->session->set('user',$user->getId());
                }else{
                    $this->response->redirect('login');
                }
            }else{
                $this->response->redirect('login');
            }
        }else{
            $this->response->redirect('login');
        }
        //$this->session->set('user','test');
        $this->response->redirect('index');
    }
}
