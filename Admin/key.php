<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 15/9/21
 * Time: 上午11:23
 */
    include_once "common.php";
    include_once "Model/Key.class.php";
    include_once "Model/Common.class.php";
session_start();
if(!isset($_SESSION['user']) || empty($_SESSION['user'])){
    header("Location:login.php");
}

    $action = $_GET['action'];
    if($action == "keyList"){
        $key = new Key();
        $pageNum = isset($_GET['pageNum'])?$_GET['pageNum']:1;
        $pageSize = isset($_GET['pageSize'])?$_GET['pageSize']:10;
        $result = $key->getKeyList($pageNum,$pageSize);
        foreach($result as $key=>$value){
            if(($value['b1_openid'] != null || !empty($value['b1_openid'])) ||($value['b2_openid'] == null || empty($value['b2_openid']))){
                $result[$key]['buy_times'] = 1;
            }else if(($value['b1_openid'] != null || !empty($value['b1_openid'])) ||($value['b2_openid'] != null || !empty($value['b2_openid']))){
                $result[$key]['buy_times'] = 2;
            }else{
                $result[$key]['buy_times'] = 0;
            }

            $status = $result[$key]['status'];
            if(($status > "10" && $status < 20) || ($status>-10 && $status <0)){
                $result[$key]['statusDesc'] = "跟进中";
            }else if($status >= 100 && $status <999){
                $result[$key]['statusDesc'] = "已成交";
            }else if($status == 0){
                $result[$key]['statusDesc'] = "未购买";
            }else if($status >-20 && $status <-10){
                $result[$key]['statusDesc'] = "申诉中";
            }



        }
        $tpl->assign("result",$result);
        $tpl->display("key_manage.html");
    }
    else if($action == "getDetail"){
        $key = new Key();
        $key->id = $_POST['key_id'];
        $result['key'] = $key->getOne();

        //获取买家相关信息
        $key->user_openid = $result['key']['user_openid'];
        $result['publisher'] = $key->getMember();

        //判断购买次数
        if($result['key']['b1_openid'] != null || !empty($result['key']['b1_openid'] ||$result['key']['b2_openid']== null || empty($result['key']['b2_openid']))){
            $result['buy_times'] = 1;
            $key->num = 1;
            $key->b1_openid = $result['key']['b1_openid'];
            $result['buyer'] = $key->getKeyDetail();
        }else if($result['key']['b1_openid'] != null || !empty($result['key']['b1_openid']) ||$result['key']['b2_openid'] != null || !empty($result['key']['b2_openid'])){
            $result['buy_times'] = 2;
            $key->num = 2;
            $key->b1_openid = $result['key']['b1_openid'];
            $key->b2_openid = $result['key']['b2_openid'];
            $result['buyer'] = $key->getKeyDetail();
        }else{
            $result['buy_times'] = 0;
            $result['buyer'] = array();
        }

        //判断线索状态
        $status = $result['key']['status'];


        echo json_encode($result);
    }
    else if($action == "keyShow"){
        $key = new Key();
        //获取线索总数
        $result['total'] = $key->getKeyNum();
        $result['used']   = $key->getUsedNum();
        $result['unused']   = $key->getUnUsedNum();


        //根据获取前七日的数据
        $date=array(
            date("Y-m-d",strtotime("-6 day")),
            date("Y-m-d",strtotime("-5 day")),
            date("Y-m-d",strtotime("-4 day")),
            date("Y-m-d",strtotime("-3 day")),
            date("Y-m-d",strtotime("-2 day")),
            date("Y-m-d",strtotime("-1 day")),
            date("Y-m-d",time()),
        );

        foreach($date as $value){
            $key->date = $value;
            $dateData1 = $key->getDateData();           //每日所有发布数据
            $key->in = "0";
            $status = 0;
            $dateData2 = $key->getDateData($status);    //已购买线索
            $rate1[] = $dateData2['num']/$dateData1['num']?$dateData2['num']/$dateData1['num']*100:0;
        }

        $tpl->assign("date",json_encode($date));
        $tpl->assign("rate1",json_encode($rate1));

        //获取近七周的数据
        $week = array(
            date("Y-m-d",strtotime("-6 week Sunday")),
            date("Y-m-d",strtotime("-5 week Sunday")),
            date("Y-m-d",strtotime("-4 week Sunday")),
            date("Y-m-d",strtotime("-3 week Sunday")),
            date("Y-m-d",strtotime("-2 week Sunday")),
            date("Y-m-d",strtotime("-1 week Sunday")),
            date("Y-m-d",time()),

        );

        foreach($week as $k => $value){
            $key->startDate = $week[$k];
            $key->endDate = $week[$k+1];
            $dateData1 = $key->getWeekData();
            $key->in = "0";
            $status = 0;
            $dateData2 = $key->getWeekData($status);    //已购买线索
            $rate2[] = $dateData2['num']/$dateData1['num']?$dateData2['num']/$dateData1['num']*100:0;
        }
        $tpl->assign("week",json_encode($week));
        $tpl->assign("rate2",json_encode($rate2));

        //获取近七月的数据
        $month = array(
            date("Y-m",strtotime("-6 month")),
            date("Y-m",strtotime("-5 month")),
            date("Y-m",strtotime("-4 month")),
            date("Y-m",strtotime("-3 month")),
            date("Y-m",strtotime("-2 month")),
            date("Y-m",strtotime("-1 month")),
            date("Y-m",time()),
        );

        foreach($month as $k => $value){
            $key->startDate = $month[$k];
            $key->endDate = $month[$k+1];
            $dateData1 = $key->getMonthData();
//            var_dump($dateData1);
            $key->in = "0";
            $status = 0;
            $dateData2 = $key->getMonthData($status);    //已购买线索
            $rate3[] = $dateData2['num']/$dateData1['num']?$dateData2['num']/$dateData1['num']*100:0;
        }
        $tpl->assign("month",json_encode($month));
        $tpl->assign("rate3",json_encode($rate3));

        $tpl->assign("result",$result);

        $tpl->display("key_list.html");
    }
    else if($action == "shareOrder"){
        $key = new Key();
        $result = $key->getShareOrders();
        $tpl->assign("result",$result);
        $tpl->display("key_share_order.html");
    }
    else if($action == "shareOrderDetail"){

        $id = (int)$_POST['id'];
        $result = array();
        $key = new Key();
        $key->id = $id;
        $orders = $key->getShareOrderDetail();      //晒单详情


        $orders['content'] = unserialize($orders['content']);

        foreach($orders['content'] as $k=>$value){
            if($value==""){
                $orders['content'][$k] = "无数据";
            }
        }
        $key->id = $orders['key_id'];
        $keys = $key->getOne();
        $result = array(
            "orders"=>$orders,
            "keys" => $keys
        );

        echo json_encode($result);
    }
    //晒单审核
    else if($action == "shareOrderVerify"){
        $key = new Key();
        $key->id = $_POST['id'];
        if($_POST['result'] == 0){
            $key->status = 2;   //审核不通过
        }elseif($_POST['result'] == 1){
            $key->status = 1;   //审核通过
        }
        $key->remark = trim($_POST['remark']);
        $key->verify_time = date("Y-m-d H:i:s",time());
        if($key->verifyShareOrder()){
            echo 1;
        }else{
            echo -1;
        }


    }
