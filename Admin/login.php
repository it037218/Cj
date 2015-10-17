<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 15/9/14
 * Time: 上午9:37
 */
    include_once "common.php";
    include_once "Model/Member.class.php";
    $action = $_GET['action'];
    session_start();
    if(isset($action) && !empty($action) && $action == "signin"){

        if(empty($_POST['tel']) || empty($_POST['password'])){
            echo -1;
        }else{
            $m = new Member();
            $m->telephone = trim($_POST['tel']);
            $m->password = md5(trim($_POST['password']));
            $m->type = 1;
            $result = $m->checkLogin();
            if($result == false||empty($result)){
                echo -1;
            }else{
                $_SESSION['user'] = $result;
                echo 1;
            }
        }
    }else{
        $tpl->display("login.html");
    }


