<?php

use Phalcon\Mvc\Controller;
use Helper\Date as FormatDate;

class IndexController extends ControllerBase
{
    public function indexAction()
    {
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
    }
}
