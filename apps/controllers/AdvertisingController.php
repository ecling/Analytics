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
        $postData = $_POST;
        if(isset($postData['name'])&&isset($postData['image_url'])){
            $ad = Advertising::findFirst([
                'conditions' => [
                    'name' => $postData['name'],
                    'image_url' => $postData['image_url']
                ]
            ]);
            if($ad){
                $this->response->redirect('advertising/list');
            }else{
                $time = date('Y-md-d H:i:s',time());
                $ad = new Advertising();
                $ad->name = $postData['name'];
                $ad->image_url = $postData['image_url'];
                $ad->created_at = $time;
                $ad->updated_at = $time;
                if($ad->save() === false){
                    echo 'error';
                    $this->response->redirect('advertising/edit');
                }else{
                    $this->response->redirect('advertising/list');
                }
            }
        }else{
            $this->response->redirect('advertising/edit');
        }
    }

    public  function listAction(){
        $this->view->ad = Advertising::find();
    }
}
