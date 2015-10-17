<?php

    namespace Addons\Chengjiaojia\Model;
    use Think\Model;

    class CreditModel extends Model{
        public function __construct(){
            $this->user_openid = "";
            $this->id = "";

        }

        //获取某条记录
        public function getOne(){
            $result =  M("user")->find();
            echo 111;
            return $result;
        }

        /*
         * 获取积分明细
         * */
        public function getCreditList(){
            $result = M("credit")->where(" user_openid = '".$this->user_openid."'")->order("create_time desc")->select();
            return $result;
        }

        /*
         * 用户积分修改
         * */
        public function creditChange(){
            $result = M('credit')->where("user_openid =".$this->user_openid)->setInc("credit");
            return $result;
        }

        /*
         * 积分变化日志
         * */
        public  function addCreditLog($data){
            $result = M("cj_credit_log")->data($data)->add();
            return $result;
        }




    }


?>