<?php

namespace Addons\Chengjiaojia;
use Common\Controller\Addon;

/**
 * 橙蕉价插件
 * @author 无名
 */

    class ChengjiaojiaAddon extends Addon{

        public $info = array(
            'name'=>'Chengjiaojia',
            'title'=>'橙蕉价',
            'description'=>'橙蕉价',
            'status'=>1,
            'author'=>'无名',
            'version'=>'0.1',
            'has_adminlist'=>1,
            'type'=>1         
        );

	public function install() {
		$install_sql = './Addons/Chengjiaojia/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Chengjiaojia/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }