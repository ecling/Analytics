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

        //测试去除登录验证
        if($this->session->has('user')){
			
        }else{
            $this->response->redirect('/login');
			return;
        }
    }
}
