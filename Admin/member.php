<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 15/9/14
 * Time: 上午1:52
*/
    include_once "common.php";
    include_once "Model/Member.class.php";
    include_once "Model/Common.class.php";
session_start();
if(!isset($_SESSION['user']) || empty($_SESSION['user'])){
    header("Location:login.php");
}

    $action = $_GET['action'];

    //所有用户列表
    if($action == "index"){
        //获取城市列表
        $common = new Common();
        $city_id="";
        $status="";
        $telephone="";
        $name="";
        $city = $common->getCityList();
        $tpl->assign("city",$city);

        $pageNum = isset($_GET['pageNum'])?$_GET['pageNum']:1;
        $pageSize = isset($_GET['pageSize'])?$_GET['pageSize']:10;
        //定义上一页跳转url
        $preUrl = $pageNum==1?"#":"./member.php?action=index&pageNum=".($pageNum-1)."&pageSize=".$pageSize;
        //定义下一页跳转url
        $nextUrl = $pageNum==$pages?"#":"./member.php?action=index&pageNum=".($pageNum+1)."&pageSize=".$pageSize;
        //定义首页跳转url
        $firstUrl = $pageNum==1?"#":"./member.php?action=index&pageNum=1&pageSize=".$pageSize;
        //定义末页跳转url
        $lastUrl = $pageNum==$pages?"#":"./member.php?action=index&pageNum=".$pages."&pageSize=".$pageSize;

        //待审核页面
        $member = new Member();

        //城市名
        if(isset($_GET['city_id']) && !empty($_GET['city_id'])){
            $member->city_id = $_GET['city_id'];
            $preUrl .="&city_id=".$_GET['city_id'];
            $nextUrl .= "&city_id=".$_GET['city_id'];
            $firstUrl .= "&city_id=".$_GET['city_id'];
            $lastUrl .= "&city_id=".$_GET['city_id'];
            $tpl->assign("city_id",$_GET['city_id']);
        }
        //电话号码
        if(isset($_GET['telephone']) && !empty($_GET['telephone'])){
            $member->telephone = $_GET['telephone'];

            $preUrl .="&telephone=".$_GET['telephone'];
            $nextUrl .= "&telephone=".$_GET['telephone'];
            $firstUrl .= "&telephone=".$_GET['telephone'];
            $lastUrl .= "&telephone=".$_GET['telephone'];

            $tpl->assign("telephone",$_GET['telephone']);
        }
        //真实姓名
        if(isset($_GET['name']) && !empty($_GET['name'])){
            $member->name = $_GET['name'];

            $preUrl .="&name=".$_GET['name'];
            $nextUrl .= "&name=".$_GET['name'];
            $firstUrl .= "&name=".$_GET['name'];
            $lastUrl .= "&name=".$_GET['name'];
            $tpl->assign("name",$_GET['name']);
        }
        //状态
        if(isset($_GET['status'])){
            $member->status = $_GET['status'];

            $preUrl .="&status=".$_GET['status'];
            $nextUrl .= "&status=".$_GET['status'];
            $firstUrl .= "&status=".$_GET['status'];
            $lastUrl .= "&status=".$_GET['status'];
            $tpl->assign("status",$_GET['status']);
        }

        $result = $member->getMemberList($pageNum,$pageSize);
//        $numResult = $member->getMemberNum();
        //总人数为
        $num = $result['num'];
        $tpl->assign("num",$num);
        $tpl->assign("result",$result['rows']);

        //总页数
        $pages = ceil($num/$pageSize);
        $tpl->assign("pages",$pages);
        $tpl->assign("preUrl",$preUrl);
        $tpl->assign("nextUrl",$nextUrl);
        $tpl->assign("firstUrl",$firstUrl);
        $tpl->assign("lastUrl",$lastUrl);
        $tpl->assign("currentPage",$pageNum);
        $tpl->assign("title","用户管理");
        $tpl->display("Member_list.html");
    }
    //待审核页面
    elseif($action == "verify"){
        $pageNum = isset($_GET['pageNum'])?$_GET['pageNum']:1;
        $pageSize = isset($_GET['pageSize'])?$_GET['pageSize']:10;

        //待审核页面
        $member = new Member();
        $member -> status = 3;
        $result = $member->getMemberList($pageNum,$pageSize);

//        $numResult = $member->getMemberNum();
        //总人数为
        $num = $result['num'];
        $tpl->assign("result",$result['rows']);

        //总页数
        $pages = ceil($num/$pageSize);

        //定义上一页跳转url
        $preUrl = $pageNum==1?"#":"./member.php?action=verify&pageNum=".($pageNum-1)."&pageSize=".$pageSize;
        $tpl->assign("preUrl",$preUrl);

        //定义下一页跳转url
        $nextUrl = $pageNum==$pages?"#":"./member.php?action=verify&pageNum=".($pageNum+1)."&pageSize=".$pageSize;
        $tpl->assign("nextUrl",$nextUrl);

        //定义首页跳转url
        $firstUrl = $pageNum==1?"#":"./member.php?action=verify&pageNum=1&pageSize=".$pageSize;
        $tpl->assign("firstUrl",$firstUrl);

        //定义末页跳转url
        $lastUrl = $pageNum==$pages?"#":"./member.php?action=verify&pageNum=".$pages."&pageSize=".$pageSize;
        $tpl->assign("lastUrl",$lastUrl);
        $tpl->assign("currentPage",$pageNum);
        $tpl->assign("title","用户管理-待审核");
        $tpl->display("Member_verify.html");

    }
    //已审核也也页面
    else if($action == "verified"){
        $m = new Member();
        //查找最近审核记录
        $result = $m->getRecentVerify();
        $tpl->assign("result",$result);
        $tpl->display("Member_verified.html");
    }
    //修改审核用户列表
    else if($action == "editVerify"){
        $m = new Member();
        $result= $m->getEditVerify();
        $tpl->assign("result",$result);
        $tpl->display("Member_edit.html");
    }
    //获取实名认证用户提交资料明细
    else if($action == "editInfo"){
        $m = new Member();
        $m->user_id = $_POST['id'];
        $result = $m->getEditInfo();
        echo json_encode($result);
    }

    //审核操作
    else if($action == "verifyResult"){
        $m = new Member();
        $m->user_id = $_POST['user_id'];

        if($_POST['verify_result'] == 0){
            $m->status = 5;
        }else if($_POST['verify_result'] == 1){
            $m->status = 4;
        }
        $m->verify_time = date("Y-m-d H:i:s",time());

        $info = $m->getMemberDeatail();

        if($m->verify()){
            //发送模板消息
            $common = new Common();
            $access_token = $common->get_access_token();
            $data['user_openid'] = $info['user_openid'];
            if($_POST['verify_result'] == 0){
                $data['first'] = "您提交的资料不齐全，现在就去补充吧！";
                $data['keyword1'] = "现在就去补充资料吧！";
            }else if($_POST['verify_result'] == 1){
                $data['first'] = "您提交的资料已经审核通过！";
                $data['keyword1'] = "您提交的资料已经审核通过！";
            }
            $data['keyword2'] = "admin";
            $data['keyword3'] = date("Y-m-d H:i:s",time());
            sendVerifyResult($data,$access_token);

            //获取用户当前提交信息，并保存到审核记录表中
            $info = $m->getMemberDeatail();
            $content['verify_result'] = $_POST['verify_result'];
            $content['user_id'] = $_POST['user_id'];
            $content['verify_time'] = date("Y-m-d H:i:s",time());
            $content['verify_name'] = $_SESSION['realname'];
            $content['user_openid'] = $info['user_openid'];
            $content['avatar'] = $info['avatar'];
            $content['realname'] = $info['realname'];
            $content['nickname'] = $info['nickname'];
            $content['card_id1'] = $info['card_id1'];
            $content['card_id2'] = $info['card_id2'];
            $content['business_card'] = $info['business_card'];
            $content['telephone'] = $info['telephone'];
            $content['city'] = $info['city'];
            $content['district'] = $info['district'];
            $content['brand'] = $info['brand'];
            $content['shop'] = $info['shop'];
            $content['card_number'] = $info['card_number'];
            if($m->addVerifyLog($content)){
                echo 1;exit;
            }else{
                echo -1;exit;
            }
        }else{
            echo -1;exit;
        }

    }



    else if($action == "getDetail"){
        $id = $_POST['id'];
        $m = new Member();
        $m->user_id = $id;
        $info = $m->getMemberDeatail();
        echo json_encode($info);
    }


    //实名认证审核
    else if($action == "editVerifyResult"){
        $m = new Member();
        $m->user_id = $_POST['user_id'];

        if($_POST['verify_result'] == 0){
            //审核不通过
            $m->status = 2;
        }else if($_POST['verify_result'] == 1){
            //审核通过
            $m->status = 1;
        }
        $m->verify_time = date("Y-m-d H:i:s",time());

        $info = $m->getEditInfo();
        if($m->editVerify()){
//            echo 111;exit;
            if($_POST['verify_result'] == 0){
                //审核不通过
                $data['status'] = 5;
                $data['realStatus'] = 1;
                $m->user_openid = $info['user_openid'];
                if($m->updateInfo($data)){
                    echo 1;
                }else{
                    echo -1;
                }
            }else if($_POST['verify_result'] == 1){
                //审核通过
//                $m->status = 4;
                $data = $info;
                unset($data['id']);
                unset($data['user_openid']);
                unset($data['create_time']);
                $data['status'] = 4;
                $m->user_openid = $info['user_openid'];
                if($m->updateInfo($data)){
                    echo 1;
                }else{
                    echo -1;
                }
            }
            $m->user_openid = $info['user_openid'];

            //发送模板消息
            $common = new Common();
            $access_token = $common->get_access_token();
            $data['user_openid'] = $info['user_openid'];
            if($_POST['verify_result'] == 0){
                $data['first'] = "您提交的资料不齐全，现在就去补充吧！";
                $data['keyword1'] = "现在就去补充资料吧！";
            }else if($_POST['verify_result'] == 1){
                $data['first'] = "您提交的资料已经审核通过！";
                $data['keyword1'] = "您提交的资料已经审核通过！";
            }
            $data['keyword2'] = "橙蕉";
            $data['keyword3'] = date("Y-m-d H:i:s",time());
            sendVerifyResult($data,$access_token);


            //获取用户当前提交信息，并保存到审核记录表中
//            $info = $m->getMemberDeatail();
//            $content['verify_result'] = $_POST['verify_result'];
//            $content['user_id'] = $_POST['user_id'];
//            $content['verify_time'] = date("Y-m-d H:i:s",time());
//            $content['verify_name'] = $_SESSION['user']['realname'];
//            $content['user_openid'] = $info['user_openid'];
//            $content['avatar'] = $info['avatar'];
//            $content['realname'] = $info['realname'];
//            $content['nickname'] = $info['nickname'];
//            $content['card_id1'] = $info['card_id1'];
//            $content['card_id2'] = $info['card_id2'];
//            $content['business_card'] = $info['business_card'];
//            $content['telephone'] = $info['telephone'];
//            $content['city'] = $info['city'];
//            $content['district'] = $info['district'];
//            $content['brand'] = $info['brand'];
//            $content['shop'] = $info['shop'];
//            $content['card_number'] = $info['card_number'];
//            if($m->addVerifyLog($content)){
//                echo 1;exit;
//            }else{
//                echo -1;exit;
//            }
        }else{
            echo -1;exit;
        }

    }



    //审核结束后，通知用户
    function sendVerifyResult($data,$access_token){

        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $access_token;
        $param = array(
            "touser" => $data['user_openid'],
            "template_id"=>"YLsKVjRQQCGAj4okj3CAdOcqBaJRdF_HPJgOzvKHOjg",
            "url" => "http://mp.chengjiao8.cn/index.php?s=/addon/Chengjiaojia/Member/reg_result",
            "topcolor" => "#FF0000",
            "data" => array(
                "first" => array(
                    "value" => $data['first'],
                    "color" => "#173177"
                ),
                "keyword1" => array(
                    "value" => $data['keyword1'],
                    "color" => "#173177"
                ),
                "keyword2" => array(
                    "value" => $data['keyword2'],
                    "color" => "#173177"
                ),
                "keyword3" => array(
                    "value" => $data['keyword3'],
                    "color" => "#173177"
                ),
                "remark" => array(
                    "value" => "现在就进入吧！",
                    "color" => "#173177"
                ),

            )
        );
        $context['http'] = array
        (
            'method' => 'POST',
            'content' => json_encode($param)
        );
        $result = file_get_contents($url, false, stream_context_create($context));
        $result = json_decode($result,true);
        $msg['user_openid'] = $data['user_openid'];
        $msg['errmsg'] = $result['errmsg'];
        $msg['msg_id'] = $result['msgid'];
        $msg['url'] = $data['url'];
        $msg['create_time'] = date("Y-m-d H:i:s",time());
        $msg['remark'] =$data['keyword1'];
        $msg['template_id'] = $param['template_id'];
}