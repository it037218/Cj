<?php

namespace Addons\Chengjiaojia\Controller;
use Addons\Chengjiaojia\Controller\BaseController;

class TestController extends BaseController{
        public function __construct(){
            //获取用户openid
            $data = get_wx_id();
            //检查用户是否注册
            check_register($data);
        }



        public function index(){


        }
 }