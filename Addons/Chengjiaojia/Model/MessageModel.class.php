<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2015/7/1
 * Time: 11:59
 */
    namespace Addons\Chengjiaojia\Model;

    use Think\Model;
    class MessageModel extends Model{
        //获取用户未读消息数量
        public function getUnreadNum($user_openid){
            $m = M("cj_message");
            return $m->where("status = 0 and user_openid = '".$user_openid."'")->count();
        }


        //获取用户消息
        public function getMessageList($data){
            $m = M("cj_message");
            $result['rows'] = $m->where("user_openid ='".$data['user_openid']."'")->order("create_time desc")->select();
            $result['total'] = $m->where("user_openid ='".$data['user_openid']."'")->count();
            return $result;
        }

        //获取用户消息详情
        public function getMessageDetail($id){
            $m = M("cj_message");
            $result = $m->where("id = ".$id)->find();
            return $result;
        }

        //获取消息状态
        public function checkStatus($id){
            return M('cj_message')->where("id = ".$id)->getField("status");
        }

        //更改消息状态
        public function changeStatus($id){
            $data['status'] = 1;
            return M("cj_message")->where("id = ".$id)->save($data);

        }

    }
