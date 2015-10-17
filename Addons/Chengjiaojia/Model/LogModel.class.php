<?php

    namespace Addons\Chengjiaojia\Model;
    use Think\Model;

    class LogModel extends Model{
        //生成日志
        public function addLog($data){
            $result = M("cj_log")->data($data)->create();
            return $result;
        }

        //查看日志
        public function getLog(){
            $result = M("cj_log")->select();
            return $result;
        }


    }



?>