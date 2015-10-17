<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 15/9/14
 * Time: 上午1:52
 */
    include_once "common.php";
    include_once "Model/Common.class.php";
    include_once "Model/Mall.class.php";
    session_start();
    if(!isset($_SESSION['user']) || empty($_SESSION['user'])){
        header("Location:login.php");
    }
    $action = $_GET['action'];
    if($action  == "list"){
        $mall = new Mall();
        if(isset($_GET['status']) && $_GET['status'] == 1){
            $mall->status = 1;
        }else if(isset($_GET['status']) && $_GET['status'] == 0){
            $mall->status = 0;
        }
        $status = isset($_GET['status'])?$_GET['status']:2;
        $tpl->assign("status",$status);
        $result = $mall->orderList();
        $num = $result['num'];

        $tpl->assign("result",$result['rows']);
        $tpl->display("mall.html");
    }
    //审核
    else if($action == "send"){
        if(!isset($_POST['id']) || empty($_POST['id'])){
            echo -1;
            exit;
        }else{
            $mall = new Mall();
            $mall->id = $_POST['id'];
            if($_POST['result'] == 1){
                $mall->status = 1;
            }
            if($mall->changeStatus()){
                echo 1;
            }else{
                echo -1;
            }
        }
    }
    //获取用户兑换记录
    else if($action == "exchangeList"){
        $user_openid = $_POST['user_openid'];

        $result = $mall->getExchangeList();
        echo json_encode($result);
    }