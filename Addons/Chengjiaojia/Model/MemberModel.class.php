<?php

namespace Addons\Chengjiaojia\Model;
use Think\Model;

class MemberModel extends Model{
    /*
     * 查看用户openid是否存在
     * */
    public function check($data){
        $result = M("cj_member")->where("user_openid = '".$data."'")->getField("status");
        return $result;
    }
    /*
     * 查看用户状态
     * status
     * */
    public function checkStatus($user_openid){
        $result = M('cj_member')->where("user_openid = '".$user_openid."'")->getField("status");
        return $result;
    }


    /*
     * 创建用户信息
     * user_openid  status
     * */
    public function addMember($data){
        $result = M("cj_member")->data($data)->add();
        return $result;
    }


    public function getOne($data){
        $result =  M("cj_member")->where("user_openid = '".$data."'")->find();
        return $result;
    }

    /*
     * 注册用户信息
     * */
    public function saveMember($data,$user_openid){
        $result  = M("cj_member")->where("user_openid = '".$user_openid."'")->save($data);
        return $result;
    }


    /*
     * 更新用户信息
     * */
    public function updateMember($data,$user_openid){

        return M("cj_member")->data($data)->where("user_openid = '".$user_openid."'")->save();
    }

    /*
     * 用户线索有效度
     * */

    public function keyValues(){

    }

    //查询用户信息
    public function getMemberInfo($user_openid){
        $member = M("cj_member");

        $result = $member->where("user_openid='".$user_openid."'")->find();

        return $result;
    }

    //用户积分扣除
    public function creditChange($res,$user_openid){

        M("cj_member")->where("user_openid = '".$user_openid."'")->setInc("frozen_credit",$res['frozen_credit']);
        return M("cj_member")->where("user_openid = '".$user_openid."'")->setDec("use_credit",$res['use_credit']);
    }

    //获取用户积分明细
    public function getCreditInfo($user_openid){
        $data['user_openid'] = $user_openid;
        return M("cj_credit_log")->where($data)->order("create_time desc")->select();
    }

}


?>