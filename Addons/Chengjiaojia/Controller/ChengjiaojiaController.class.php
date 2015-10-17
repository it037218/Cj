<?php

namespace Addons\Chengjiaojia\Controller;
use Addons\Chengjiaojia\Controller\BaseController;

class ChengjiaojiaController extends BaseController{
    public function index(){
        $this->show ( ONETHINK_ADDON_PATH . 'Chengjiaojia/View/index.html' );
    }
    

    public function menu(){
    	$this->show(ONETHINK_ADDON_PATH . 'Chengjiaojia/index.html');
    }

    public function content(){

    }
}
