<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/30 0030
 * Time: 11:41
 */

use Phalcon\Mvc\Controller;

class WebsiteController extends ControllerBase
{
    public function indexAction(){
        $this->view->website = Website::find();
    }

    public function editAction()
    {
        //$this->view->product = Products::findFirst();

    }

    public function saveAction(){
        $postData = $_POST;
        if(isset($postData['name'])&&isset($postData['domain'])){
            $website = Website::findFirst([
                'conditions' => [
                    'domain' => $postData['domain']
                ]
            ]);
            if($website){
                $this->response->redirect('website');
            }else{
                $time = date('Y-m-d H:i:s',time());
                $website = new Website();
                $website->name = $postData['name'];
                $website->domain = $postData['domain'];
                $website->default = 0;
                $website->created_at = $time;
                $website->updated_at = $time;
                if($website->save() === false){
                    echo 'error';
                    $this->response->redirect('website/edit');
                }else{
                    $this->response->redirect('website');
                }
            }
        }else{
            $this->response->redirect('website/edit');
        }
    }

    public function changeAction(){
        if(isset($_GET['id'])){
            $this->session->set('website',(string)$_GET['id']);
            $this->response->redirect($_SERVER['HTTP_REFERER']);
        }
    }
}
