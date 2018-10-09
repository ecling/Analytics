<?php

use Phalcon\Mvc\Controller;

class AdvertisingController extends ControllerBase
{
    public function indexAction()
    {
        $this->view->ad = Advertising::find();
    }

    public function editAction(){
        if(isset($_GET['id'])){
            $ad = Advertising::findFirst([
                [
                    'ad_id' => floatval($_GET['id'])
                ]
            ]);
            $this->view->ad = $ad;
        }
    }

    public function saveAction(){
        $postData = $_POST;
        if(isset($postData['name'])&&strlen($postData['name'])>2&&isset($postData['image_url'])&&strlen($postData['image_url'])>10){
            if(isset($postData['ad_id'])){
                $ad = Advertising::findFirst([
                    [
                        'ad_id' => (float)$postData['ad_id']
                    ]
                ]);
            }else {
                $ad = Advertising::findFirst([
                    'conditions' => [
                        'name' => $postData['name'],
                        'image_url' => $postData['image_url']
                    ]
                ]);
            }

            $time = date('Y-m-d H:i:s',time());
            if($ad){
                //$this->response->redirect('advertising/list');
            }else {
                $ad = new Advertising();
                $ids = new Ids();
                $ad->ad_id = $ids->getAdId();
                $ad->created_at = $time;
            }
            if(isset($postData['status'])){
                $ad->status = 2;
            }else{
                $ad->status = 1;
            }
            $ad->name = $postData['name'];
            $ad->image_url = $postData['image_url'];
            $ad->updated_at = $time;

            if($ad->save() === false){
                echo 'error';
                $this->response->redirect('advertising/edit');
            }else{
                $this->response->redirect('advertising/list');
            }
        }else{
            $this->response->redirect('advertising/edit');
        }
    }

    public  function listAction(){
        $website_id = $this->session->get('website');
        $this->view->ad = Advertising::find([
            [
                'website_id' => $website_id
            ]
        ]);
    }
}
