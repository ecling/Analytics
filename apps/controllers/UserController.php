<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/30 0030
 * Time: 11:48
 */

use Phalcon\Mvc\Controller;

class UserController extends ControllerBase
{
    public function indexAction(){
        $this->view->user = User::find();
    }
    public function editAction(){

    }
    public function saveAction(){
        $postData = $_POST;
        if(isset($postData['email'])&&isset($postData['password'])){
            $user = User::findFirst([
                'conditions' => [
                    'email' => trim($postData['email'])
                ]
            ]);
            if(!$user) {
                $time = date('Y-m-d H:i:s',time());
                $user = new User();
                $user->email = trim($postData['email']);
                $user->password = md5(trim($postData['password']));
                $user->created_at = $time;
                $user->updated_at = $time;
                if ($user->save() === false) {
                    echo 'error';
                } else {
                    $this->response->redirect('user');
                }
            }else{
                $this->response->redirect('user');
            }
        }
    }
}