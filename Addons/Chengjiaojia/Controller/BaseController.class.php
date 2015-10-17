<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2015/6/3
 * Time: 20:44
 */

namespace Addons\Chengjiaojia\Controller;

use Home\Controller\AddonsController;

class BaseController extends AddonsController{

    //初始化
    function _initialize(){
        parent::_initialize();
        $controller = strtolower ( _CONTROLLER );
        //定义模板路径
        define ( 'CUSTOM_TEMPLATE_PATH', ONETHINK_ADDON_PATH . 'Chengjiaojia/View');
        //定义控制器路劲
//        define ( 'CUSTOM_CONTROLLER_PATH', ONETHINK_ADDON_PATH . 'Chengjiaojia/View');
    }

}