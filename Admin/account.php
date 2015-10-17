<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 15/10/14
 * Time: 上午10:04
 */

include_once "common.php";
include_once "Model/Account.class.php";
include_once "Model/Common.class.php";

$action = $_GET['action'];
//用户资金状况
if($action == "memberAccount"){
    $account = new Account();
    $result = $account->getMemberAccount();

    foreach($result as $key=>$value){
        $account->user_openid = $value['user_openid'];

        //获取用户累计充值金额
        $res = $account->getRechargeTotal();
        $result[$key]['totalRecharge'] = $res['total']?$res['total']:"0.00";

        //获取用户累计提现总额
        $res = $account->getCashTotal();
        $result[$key]['totalCash'] = $res['total']?$res['total']:"0.00";

    }
    $tpl->assign("result",$result);
    $tpl->display("account_member_list.html");
}
//资金使用明细
else if($action == "accountList"){
    $account = new Account();

    $result = $account->getAccountList();

    $tpl->assign("result",$result);
    $tpl->display("account_log_list.html");

}
//提现管理
else if($action == "accountCashList"){
    $account = new Account();

    if(isset($_GET['status'])){
        $account->status = (int)$_GET['status'];
    }else if(isset($_GET['name'])){
        $account->name = trim(($_GET['name']));
    }
    $result = $account->getCashList();
    if(empty($result)){
        $result = 0;
    }
    $tpl->assign("result",$result);
    $tpl->display("account_cash.html");
}
//查询某用户资金记录
else if($action == "getMemberAccountDetail"){
    $account = new Account();
    $account->user_openid = trim($_POST['user_openid']);
    $result = $account->getAccountList();
    if($result){
        echo json_encode($result);
    }else{
        echo -1;
        exit;
    }
}
else if($action =="recharge"){
    $tpl->display("account_publish_recharge.html");

}
//根据手机号检查用户信息
else if($action == "checkMember"){
    $telephone = trim($_POST['telephone']);
    if($telephone == ""){
        echo -1;exit;   //没有填写手机号
    }else{
        $account = new Account();
        $account->telephone = $telephone;
        $result = $account->checkMember();
        if($result){
            foreach($result as $key=>$value){
                if($value['status'] == 4){
                    $result[$key]['status'] = "正常状态";
                }else if($value['status'] == ""){

                }
            }


            echo json_encode($result);  //输出用户信息
            exit;
        }else{
            echo -2;        //用户信息验证失败
            exit;
        }

    }
}