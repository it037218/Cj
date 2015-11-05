<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 15/9/29
 * Time: 上午10:07
 */


class Mall{
    var $orderTable = "wp_cj_mall_order";
    var $memberTable = "wp_cj_member";
    var $commodityTable = "wp_cj_commodity";
    var $status;
    var $id;
    var $user_openid;
    var $commodity_name;
    function orderList(){

        $sql = "SELECT p1.*,p2.name,p2.city,p2.avatar,p2.nickname,p2.telephone from ".$this->orderTable." as p1 left join ".$this->memberTable." as p2 ON p2.user_openid = p1.user_openid";
        $totalSql = "SELECT COUNT(p1.id) as num FROM ".$this->orderTable." as p1 left join ".$this->memberTable." as p2 ON p2.user_openid = p1.user_openid";
        if(isset($this->status)){
            $sql .= " WHERE p1.status =".$this->status;
            $totalSql .=" WHERE p1.status = ".$this->status;
        }

        $sql .= " order by create_time desc";
        $result['rows'] = DbHelper::getInstance()->get_all($sql);
        $rst =   DbHelper::getInstance()->get_one($totalSql);
        $result['num'] = $rst['num'];
        return $result;

    }

    /*
     * 订单状态修改
     * */
    function changeStatus(){
        $data['status'] = $this->status;
        return DbHelper::getInstance()->update($this->orderTable,$data," id = ".$this->id);
    }

    /*
     * 获取用户兑换记录
     * */
    function getExchangeList(){
        $sql = "SELECT p2.*,p1.name FROM ".$this->orderTable." as p1 LEFT JOIN ".$this->memberTable." as p2 ON p1.user_openid = p2.user_openid where p1.user_openid = '".$this->user_openid."'";
        return DbHelper::getInstance()->get_all($sql);
    }

    /*
     * 获取商品列表
     * 2015-10-26
     * */
    function getCommodity(){
        $sql = "SELECT * FROM ".$this->commodityTable." WHERE 1=1";
        if(isset($this->status)){
            $sql .= " AND status = ".$this->status;
        }else if(isset($this->commodity_name)){
            $sql .=" LIKE '%".$this->commodity_name."&'";
        }
        return DbHelper::getInstance()->get_all($sql);

    }

    /*
     * 获取商品详情
     * */
    function getCommodityDetail(){
        $sql = "SELECT * FROM ".$this->commodityTable." WHERE id = ".$this->id;
        return DbHelper::getInstance()->get_one($sql);
    }

    /*
     * 新增商品数据
     * */
    function insert($data){
        return DbHelper::getInstance()->insert($this->commodityTable,$data);
    }

}

