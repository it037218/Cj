<?php

namespace Addons\Wecome\Model;

use Home\Model\WeixinModel;

/**
 * Vote模型
 */
class WeixinAddonModel extends WeixinModel {
	function reply($dataArr, $keywordArr = array()) {
		return true;
	}
	// 关注时的操作
	function subscribe($dataArr) {

		$config = getAddonConfig ( 'Wecome' ); // 获取后台插件的配置参数
		
		// 其中token和openid这两个参数一定要传，否则程序不知道是哪个微信用户进入了系统
		$param ['token'] = get_token ();
		$param ['openid'] = get_openid ();

		$scene_id = $dataArr['EventKey'];


		//判斷用戶數據是否已經進入用戶表
		$con1['user_openid'] = $param['openid'];
		$res = M("cj_member")->where($con1)->find();
		if(empty($res) || $res == null){
			$con1['subscribe_time'] = date("Y-m-d H:i:s",time());
			M("cj_member")->add($con1);
		}


		$accessToken = get_access_token();
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$accessToken."&openid=".$param['openid']."&lang=zh_CN";
		$result = file_get_contents($url);
		$result = json_decode($result,true);


		if(isset($scene_id) || !empty($scene_id)){
			$con['user_openid'] = $param ['openid'];
			$data['from_scene_id'] = substr($scene_id,stripos($scene_id,"_")+1);
			$data['create_time'] = date("Y-m-d H:i:s",time());
			$data['status'] = 0;
			$data['headimgurl'] = $result['headimgurl'];
			$data['nickname'] = $result['nickname'];
			$data['recommender'] = M("cj_member")->where("id = ".$data['from_scene_id'])->getField("user_openid");
			M("cj_member")->where($con)->save($data);
		}
		$sreach = array('[follow]', '[website]');
		$replace = array(addons_url('UserCenter://UserCenter/edit', $param), addons_url('WeiSite://WeiSite/index', $param));
		$config ['description'] = str_replace($sreach, $replace, $config ['description'] );
		
		switch ($config ['type']) {
			case '3' :
				$articles [0] = array (
						'Title' => $config ['title'],
						'Description' => $config ['description'],
						'PicUrl' => $config ['pic_url'],
						'Url' => str_replace($sreach, $replace, $config ['url'] )
				);
				$res = $this->replyNews ( $articles );
				break;
			case '2' :
				return false;
				break;
			default :
				$res = $this->replyText ( $config ['description'] );
		}
		
		return $res;
	}

	function unsubscribe($dataArr){
////		$param ['openid'] = get_openid ();
//		$con['user_openid'] = get_openid();
//
////		$data = D("common/Weixin")->getData();
//		$data['scene_id'] = 11;
//		$data['qr_pic'] = json_encode($dataArr);
//		M("cj_member")->where($con)->save($data);
	}

}
