<?php
        	
namespace Addons\Chengjiaojia\Model;
use Home\Model\WeixinModel;
        	
/**
 * Chengjiaojia的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Chengjiaojia' ); // 获取后台插件的配置参数	
		//dump($config);

	} 

	// 关注公众号事件
	public function subscribe() {
	}
	
	// 取消关注公众号事件
	public function unsubscribe() {

		$user_openid = $_SESSION['user_openid'];
		$con['user_openid'] = $user_openid;
//		$data['subscribe_time'] = date("Y-m-d H:i:s",time());
		$data['realStatus'] = 2;
		M("cj_member")->where($con)->save($data);
	}
	
	// 扫描带参数二维码事件
	public function scan() {
		return true;
	}
	
	// 上报地理位置事件
	public function location() {
		return true;
	}
	
	// 自定义菜单事件
	public function click() {
		return true;
	}	
}
        	