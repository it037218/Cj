<?php

    namespace Addons\Chengjiaojia\Model;
    use Think\Model;

    class MallModel extends Model{
        public function getCommodityList(){
            $obj = M("cj_commodity");
            $result = $obj -> order("`order`")->select();
            return $result;
        }

        public function getCommodityDetail($data){
            $result = M('cj_commodity')->where("id = ".$data['id'])->find();
            return $result;
        }

        //积分兑换
        public function createOrder($data){
            $result = M("cj_mall_order")->data($data)->add();
            return $result;
        }

        //变更库存
        public function releaseNum($id){
            return M("cj_commodity")->where("id = ".$id)->setDec("realtime_number");
        }

    }


?>