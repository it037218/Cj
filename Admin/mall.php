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
        $mall = new Mall();
        $result = $mall->getExchangeList();
        echo json_encode($result);
    }else if($action == "commodity"){
        $mall = new Mall();
        if(isset($_GET['status'])){
            $mall->status = $_GET['status'];
        }else if(isset($_GET['name'])){
            $mall->commodity_name = $_GET['name'];
        }
        $result = $mall->getCommodity();
        $tpl->assign("result",$result);
        $tpl->display("mall_commodity.html");
    }else if($action == "uploadImage"){
        $url = "";
        if(
            isset($_FILES['fileUpload']['name'])
            || $_FILES["fileUpload"]["type"] == "image/gif"
            || $_FILES["fileUpload"]["type"] == "image/jpeg"
            || $_FILES["fileUpload"]["type"] == "image/pjpeg"
        )
        {

            $fileName = "/Uploads/Mall/commodity_".time().".jpg";
            if(move_uploaded_file($_FILES["fileUpload"]["tmp_name"], $_SERVER['DOCUMENT_ROOT'].$fileName)){
                $url = $fileName;
                $message = "OK";
            }else{
                $message = "upload error";
            }
        }else{
            $message = "type error";
        }

        $result = array(
            "url"=> $url,
            "message"=>$message
        );
        echo json_encode($result);
    }else if($action == "saveCommodity"){
        $id = (int)$_POST['commodity_id'];
    }else if($action == "addCommodity"){
        $data = $_POST;
        $data['create_time'] = date("Y-m-d H:i:s",time());
        $mall = new Mall();
        $mall->insert($data);
    }else if($action == "getCommodityDetail"){
        $id = (int)$_POST['commodity_id'];
        $mall = new Mall();
        $mall->id = $id;
        $result = $mall->getCommodityDetail();
        echo json_encode($result);
    }