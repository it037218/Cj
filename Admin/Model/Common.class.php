<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 15/9/14
 * Time: 下午3:14
 */

class Common{
    var $cityTable = wp_cj_cities;
    // 获取access_token，自动带缓存功能
    function get_access_token($token = '') {
        $sql = "SELECT * FROM wp_cj_sys_user WHERE sys_user = 'chengjiaojia'";
        $rst = DbHelper::getInstance()->get_one($sql);

        $time = time();
        $access_token_update_time = strtotime($rst['access_token_update_time']);

        $expires_time = $time - $access_token_update_time;
        if ($expires_time>$rst['expires_in'] || $rst["access_token"] == null || strlen($rst["access_token"]) == 0) {
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $rst ['appid'] . '&secret=' . $rst ['app_secret'];
            $tempArr = json_decode ( file_get_contents ( $url ), true );
            $data['access_token'] = $tempArr['access_token'];
            $data['access_token_update_time'] = date("Y-m-d H:i:s",time());
            $data['expires_in'] = 7200;
            DbHelper::getInstance()->update("wp_cj_sys_user",$data,"sys_user = 'chengjiaojia'");
//            M("cj_sys_user")->where("sys_user = 'chengjiaojia'")->save($data);
            return $tempArr['access_token'];
        }else{
            return $rst['access_token'];
        }
    }

    //获取城市列表
    function getCityList(){
        $sql = "SELECT city,cityid FROM ".$this->cityTable." WHERE is_show = 1";
        return DbHelper::getInstance()->get_all($sql);
    }




}