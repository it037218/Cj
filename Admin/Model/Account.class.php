<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 15/10/14
 * Time: 下午1:33
 */

class Account{
    var $memerTable = "wp_cj_member";
    var $accountTable = "wp_cj_account";
    var $cashTable = 'wp_cj_cash';
    var $status;
    var $type;
    var $user_openid;
    var $name;
    var $telephone;
    function getAccountList(){
        $sql = "SELECT p1.*,p2.name,p2.realname FROM ".$this->accountTable." as p1 LEFT JOIN ".$this->memerTable." as p2 ON p1.user_openid = p2.user_openid WHERE p2.status = 4 ";
        if(isset($this->type)){
            $sql .= " AND p1.type=".$this->type;
        }
        if(isset($this->user_openid)){
            $sql .= " AND p1.user_openid = '".$this->user_openid."'";
        }
        $sql .= " ORDER BY p1.create_time desc";


        return DbHelper::getInstance()->get_all($sql);
    }

    function getMemberAccount(){
        $sql = "SELECT id,user_openid,name,realname,frozen_account,use_account,create_time FROM ".$this->memerTable." WHERE status = 4";
        return DbHelper::getInstance()->get_all($sql);
    }

    /*
     * 查询用户累计充值金额
     * */

    function getRechargeTotal(){
        $sql = "SELECT SUM(account) as total FROM ".$this->accountTable." WHERE type = 1 AND user_openid = '".$this->user_openid."'";
        return DbHelper::getInstance()->get_one($sql);
    }

    /*
     * 查询用户累计提现金额
     * */

    function getCashTotal(){

        $sql = "SELECT SUM(cash_account) as total FROM wp_cj_cash WHERE status = 1 AND user_openid = '".$this->user_openid."'";
        return DbHelper::getInstance()->get_one($sql);

    }

    /*
     * 查询提现情况
     * */


    function getCashList(){
        $sql = "SELECT p1.*,p2.name,p2.realname FROM ".$this->cashTable." as p1 LEFT JOIN ".$this->memerTable." as p2 ON p1.user_openid = p2.user_openid WHERE 1=1";
        if(isset($this->status)){
            $sql .= " AND p1.status = ".$this->status;
        }
        if(isset($this->name)){
            $sql .= " AND p2.name like '%".$this->name."%'";
        }
        $sql .= " ORDER BY p1.create_time DESC";
        return DbHelper::getInstance()->get_all($sql);

    }

    //检查用户信息
    function checkMember(){
        $sql = "SELECT user_openid,realname,nickname,status,telephone,use_account,frozen_account FROM ".$this->memerTable." WHERE telephone = '".$this->telephone."'";
//        echo $sql;exit;
        return DbHelper::getInstance()->get_one($sql);
    }

}
