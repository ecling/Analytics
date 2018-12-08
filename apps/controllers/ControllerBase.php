<?php


use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    public function beforeExecuteRoute()
    {
        //设置默认站点
        if($this->session->has('website')){

        }else{
            $website = Website::findFirst([
                [
                    'default'=>1
                ]
            ]);
            if($website){
                $this->session->set('website',(string)$website->getId());
            }
        }

        //公共模板变量
        $website_id = $this->session->get('website');
        $websites = Website::find();
        $this->view->websites = $websites;
        foreach ($websites as $_website) {
            $id = (string)$_website->getId();
            if($website_id==$id){
                $currentWebsite = $_website;
            }
        }
        $this->view->currentWebsite = $currentWebsite;

        //测试去除登录验证
        if(true||$this->session->has('user')){
			
        }else{
            $this->response->redirect('/login');
			return;
        }
    }
}
