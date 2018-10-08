<?php

use Phalcon\Mvc\Controller;
use Helper\Date as FormatDate;

class IndexController extends ControllerBase
{
    public function indexAction()
    {
        $test = new FormatDate();
    }
}
