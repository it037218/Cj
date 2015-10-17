<?php
    include_once "wxPay/wxHelper.class.php";
include_once "common.php";
include_once "Model/Key.class.php";
include_once "Model/Common.class.php";
    define("APPID","wx3e6b5cf323b91168");   //公众号
    define('MCHID', "1250233901");  //商户号

    function pay($money){
        $money = 100;
        $mch_billno = MCHID.date('YmdHis').rand(1000, 9999);  //订单号
        $act_name = "奖励";  //活动名称，没鸟用，目前微信版本里没显示的地方
        $openid = "o0nM4t4nSz5FDTJERyVctDjrylKw";   //朱
        //    $openid = "o0nM4tzlrljX1Co_zqJ9Z1sKYGWg";   //林
        //    $openid = "o0nM4t42EfYVh8uS9a0GCRCqPP24";   //蓉蓉
        //    $openid = "o0nM4txt6C1J0s-QYOPu8utZZ__I";   //超
        //    $openid = "o0nM4tza3aoVJ6FsMUg4BlYAQG-I";   //季
        $wxhb = new wxhb();
        $wxhb->set_para("nonce_str", $wxhb->create_noncestr()); //随机字符串
        $wxhb->set_para("mch_billno", $mch_billno); //订单号
        $wxhb->set_para("mch_id", MCHID); //商户号
        $wxhb->set_para("wxappid", APPID);  //公众号
        $wxhb->set_para("nick_name", '昵称'); //昵称，没鸟用，目前微信版本里没显示的地方
        $wxhb->set_para("send_name", '橙蕉'); //红包发送者名称
        $wxhb->set_para("re_openid", $openid); //发放者openid
        $wxhb->set_para("total_amount", $money); //付款金额，单位分
        $wxhb->set_para("min_value", 10); // 最小红包金额，单位分
        $wxhb->set_para("max_value", 200); // 最大红包金额，单位分
        $wxhb->set_para("total_num", 1); //红包发放总人数
        $wxhb->set_para("wishing", '更多豪礼查看个人中心-邀请好友'); //红包祝福诧
        $wxhb->set_para("client_ip", $_SERVER['REMOTE_ADDR']); //调用接口的机器 Ip 地址
        $wxhb->set_para("act_name", $act_name); //活动名称
        //下面的都没鸟用，目前微信版本里没显示的地方
        $wxhb->set_para("remark", '注册奖励');//备注信息
        $wxhb->set_para("logo_imgurl", ''); //商户logo的url
        $wxhb->set_para("share_content", ''); //分享文案
        $wxhb->set_para("share_url", ''); //分享链接
        $wxhb->set_para("share_imgurl", ''); //分享的图片url

        $postxml = $wxhb->create_xml();
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        $response = $wxhb->curl_post_ssl($url, $postxml);
        $responseObj = simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);

        if($responseObj->)


        $result = objectToArray($responseObj);

        var_dump(DbHelper::getInstance()->insert("wp_cj_hb",$result));

    }


    function objectToArray($e){
        $e=(array)$e;
        foreach($e as $k=>$v){
            if( gettype($v)=='resource' ) return;
            if( gettype($v)=='object' || gettype($v)=='array' )
                $e[$k]=(array)objectToArray($v);
        }
        return $e;
    }


    pay(100);



?>