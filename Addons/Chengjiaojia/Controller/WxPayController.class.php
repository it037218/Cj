<?php
    namespace Addons\Chengjiaojia\Controller;
    use Think\Model;
//初始化日志

class WxPayController extends BaseController{
    public function __construct(){
        parent::__construct();
        ini_set('date.timezone','Asia/Shanghai');
    }
    public function notify(){
//        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

//        $postObj = simplexml_load_string($GLOBALS["HTTP_RAW_POST_DATA"],'SimpleXMLElement',LIBXML_NOCDATA);
//        $data['remark'] = $postObj ;
        $data['remark'] = 123;

//        M("cj_account")->add($data);
        M("cj_account")->add($data);
    }
    //微信支付
    public function wxPay(){
        import("Org.Wxpay.WxPay#Api");
        import("Org.Wxpay.WxPay#JsApiPay");

        $money = $_POST['money'];
        $openId = $_SESSION['user_openid'];
        //保存用户充值相关信息
        $data['user_openid'] = $openId;
        $data['type'] = 1;
        $data['remark'] = "+".$money;
        $data['account'] = $money;
        $data['create_time'] = date("Y-m-d H:i:s",time());
        $data['status'] = 0;
        $data['desc'] = "充值";
        $result = M("cj_account")->add($data);

        //①、获取用户openid
        $tools = new \JsApiPay();
        //②、统一下单
        $input = new \WxPayUnifiedOrder();
        $input->SetBody("橙蕉");
        $input->SetAttach($result);         //保存充值编号
        $trade = date("YmdHis");
        $input->SetOut_trade_no("$trade");
        $input->SetTotal_fee($money*100);
//        $input->SetTotal_fee(1);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("橙蕉");
        $input->SetNotify_url("http://mp.chengjiao8.cn/index.php");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = \WxPayApi::unifiedOrder($input);
        $jsApiParameters = $tools->GetJsApiParameters($order);
//            $this->assign("jsApiParameters",$jsApiParameters);
        echo $jsApiParameters;
//            $this->show(CUSTOM_TEMPLATE_PATH."/Member/pay.html");
    }
}

//class PayNotifyCallBack extends WxPayNotify
//{
//    //查询订单
//    public function Queryorder($transaction_id)
//    {
//        $input = new \WxPayOrderQuery();
//        $input->SetTransaction_id($transaction_id);
//        $result = \WxPayApi::orderQuery($input);
//        \Log::DEBUG("query:" . json_encode($result));
//        if(array_key_exists("return_code", $result)
//            && array_key_exists("result_code", $result)
//            && $result["return_code"] == "SUCCESS"
//            && $result["result_code"] == "SUCCESS")
//        {
//            return true;
//        }
//        return false;
//    }
//
//    //重写回调处理函数
//    public function NotifyProcess($data, &$msg)
//    {
//        \Log::DEBUG("call back:" . json_encode($data));
//
//        M("cj_pay_callback")->add($data);
//        $para['id'] = $data['id'];
//        $content['status'] = 1;
//        $content['update_time'] = date("Y-m-d H:i:s",time());
//        M("cj_account")->where($para)->save($content);
//        if(!array_key_exists("transaction_id", $data)){
//            $msg = "输入参数不正确";
//            return false;
//        }
//        //查询订单，判断订单真实性
//        if(!$this->Queryorder($data["transaction_id"])){
//            $msg = "订单查询失败";
//            return false;
//        }
//        return true;
//    }
//}


