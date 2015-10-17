<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Home\Controller;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class IndexController extends HomeController {
	
	// 系统首页
	public function index() {
//		echo 111;exit;
		if(isset($GLOBALS['HTTP_RAW_POST_DATA']) && !empty($GLOBALS['HTTP_RAW_POST_DATA'])){
			$xmlObj=simplexml_load_string($GLOBALS['HTTP_RAW_POST_DATA']); //解析回调数据

			$appid=$xmlObj->appid;//微信appid
			$mch_id=$xmlObj->mch_id;  //商户号
			$nonce_str=$xmlObj->nonce_str;//随机字符串
			$sign=$xmlObj->sign;//签名
			$result_code=$xmlObj->result_code;//业务结果
			$openid=$xmlObj->openid;//用户标识
			$is_subscribe=$xmlObj->is_subscribe;//是否关注公众帐号
			$trace_type=$xmlObj->trade_type;//交易类型，JSAPI,NATIVE,APP
			$bank_type=$xmlObj->bank_type;//付款银行，银行类型采用字符串类型的银行标识。
			$total_fee=$xmlObj->total_fee;//订单总金额，单位为分
			$fee_type=$xmlObj->fee_type;//货币类型，符合ISO4217的标准三位字母代码，默认为人民币：CNY。
			$transaction_id=$xmlObj->transaction_id;//微信支付订单号
			$out_trade_no=$xmlObj->out_trade_no;//商户订单号
			$attach=$xmlObj->attach;//商家数据包，原样返回
			$time_end=$xmlObj->time_end;//支付完成时间
			$cash_fee=$xmlObj->cash_fee;
			$return_code=$xmlObj->return_code;
			if($return_code == "SUCCESS"){
				echo "SUCCESS";
				$condition["id"] = $attach;
				$status = M("cj_account")->where($condition)->getField("status");
				if($status == 0){
					$data['status'] = 1;
					$data['update_time'] = date("Y-m-d H:i:s",time());
					$condition["id"] = $attach;
					M("cj_account")->where($condition)->save($data);

					$condition1['user_openid'] = M("cj_account")->where($condition)->getField("user_openid");
					$money = M("cj_account")->where($condition)->getField("account");
					M("cj_member")->where($condition1)->setInc("use_account",$money);
				}
			}
		}




		redirect ( U ( 'home/index/main' ) );
	}
	// 系统介绍
	public function introduction() {
		$this->display ();
	}
	// 系统下载
	public function download() {
		$this->display ();
	}
	// 系统帮助
	public function help() {
		if (isset ( $_GET ['public_id'] )) {
			$map ['id'] = intval ( $_GET ['public_id'] );
			$info = M ( 'member_public' )->where ( $map )->find ();
			$this->assign ( 'token', $info ['token'] );
		} else {
			$this->assign ( 'token', '你的公众号的Token' );
		}
		$this->display ();
	}
	// 系统关于
	public function about() {
		$this->display ();
	}
	// 授权协议
	public function license() {
		$this->display ();
	}
	// 下载weiphp
	public function downloadFile() {
		M ( 'config' )->where ( 'name="DOWNLOAD_COUNT"' )->setInc ( 'value' );
		redirect ( 'http://down.weiphp.cn/weiphp.zip' );
	}
	// 远程获取最新版本号
	public function update_version() {
		die ( M ( 'update_version' )->getField ( "max(`version`)" ) );
	}
	// 远程获取升级包信息
	public function update_json() {
		$old_version = intval ( $_REQUEST ['version'] );
		$new_version = M ( 'update_version' )->getField ( "max(`version`)" );
		
		$res = array ();
		if ($old_version < $new_version) {
			$res = M ( 'update_version' )->field ( 'version,title,description,create_date' )->where ( 'version>' . $old_version )->select ();
		}
		
		die ( json_encode ( $res ) );
	}
	// 下载升级包
	public function download_update_package() {
		$map ['version'] = intval ( $_REQUEST ['version'] );
		$package = M ( 'update_version' )->where ( $map )->getField ( 'package' );
		if (empty ( $package )) {
			$this->error ( '下载的文件不存在或已被移除' );
		}
		M ( 'update_version' )->where ( $map )->setInc ( 'download_count' );
		
		redirect ( $package );
	}
	public function main() {
//		var_dump(is_login());exit;
		if (! is_login ()) {
			Cookie ( '__forward__', $_SERVER ['REQUEST_URI'] );
			$url = U ( 'home/user/login' );
			redirect ( $url );
		}
		$page = I ( 'p', 1, 'intval' );
		
		// 关键字搜索
		$map ['type'] = 1;
		$key = 'title';
		if (isset ( $_REQUEST [$key] )) {
			$map [$key] = array (
					'like',
					'%' . $_REQUEST [$key] . '%' 
			);
			unset ( $_REQUEST [$key] );
		}
		
		$row = 15;
		
		$data = M ( 'addons' )->where ( $map )->order ( 'id DESC' )->page ( $page, $row )->select ();
		$token_status = D ( 'Common/AddonStatus' )->getList ( true );
		
		foreach ( $data as $k => &$vo ) {
			if ($token_status [$vo ['name']] === '-1') {
				unset ( $data [$k] );
				// $vo ['status_title'] = '无权限';
				// $vo ['action'] = '';
				// $vo ['color'] = '#CCC';
				// $vo ['status'] = 0;
			} elseif ($token_status [$vo ['name']] === 0) {
				$vo ['status_title'] = '已禁用';
				$vo ['action'] = '启用';
				$vo ['color'] = '#CCC';
				$vo ['status'] = 0;
			} else {
				$vo ['status_title'] = '已启用';
				$vo ['action'] = '禁用';
				$vo ['color'] = '';
				$vo ['status'] = 1;
			}
		}
		
		/* 查询记录总数 */
		$count = M ( 'addons' )->where ( $map )->count ();
		
		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$this->assign ( '_page', $page->show () );
		}
		
		$this->assign ( 'list_data', $data );
		
		$res ['title'] = '插件管理';
		$res ['url'] = U ( 'main' );
		$res ['class'] = 'current';
		$nav [] = $res;
		
		$this->assign ( 'nav', $nav );
		$this->display ();
	}
	function setStatus() {
		$addon = I ( 'addon' );
		$token_status = D ( 'Common/AddonStatus' )->getList ();
		if ($token_status [$addon] === '-1') {
			$this->success ( '无权限设置' );
		}
		
		$status = 1 - I ( 'status' );
		$res = D ( 'Common/AddonStatus' )->set ( $addon, $status );
		$this->success ( '设置成功' );
	}
	
	// 宣传页面
	function leaflets() {
		$name = 'Leaflets';
		$config = array ();
		
		$map ['token'] = I ( 'get.token' );
		$addon_config = M ( 'member_public' )->where ( $map )->getField ( 'addon_config' );
		$addon_config = json_decode ( $addon_config, true );
		if (isset ( $addon_config [$name] )) {
			$config = $addon_config [$name];
			
			$config ['img'] = is_numeric ( $config ['img'] ) ? get_cover_url ( $config ['img'] ) : SITE_URL . '/Addons/Leaflets/View/default/Public/qrcode_default.jpg';
			$this->assign ( 'config', $config );
		} else {
			$this->error ( '请先保存宣传页的配置' );
		}
		define ( 'ADDON_PUBLIC_PATH', ONETHINK_ADDON_PATH . 'Leaflets/View/default/Public' );
		
		$this->display ( SITE_PATH . '/Addons/Leaflets/View/default/Leaflets/show.html' );
	}
	// 定时任务调用入口
	function cron() {
		D ( 'Home/Cron' )->run ();
		echo date ( 'Y-m-d H:i:s' ) . "\r\n";
	}
}