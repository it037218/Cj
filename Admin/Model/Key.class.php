<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 15/9/21
 * Time: 上午11:37
 */

    class Key{

        var $keyTable = "wp_cj_key";
        var $memberTable = "wp_cj_member";
        var $num ;
        var $id;
        var $status;
        var $b1_openid;
        var $b2_openid;
        var $user_openid;
        var $in;
        var $date;
        var $startDate;
        var $endDate;
        var $remark;
        var $verify_time;
        function getKeyList($pageNum,$pageSize){
            $sql = "SELECT p2.realname,p1.id,p1.create_time,p1.money,p1.custom_name,p1.city,p1.district,p1.brand,p1.series,p1.`status`,p1.time_limit,p1.b1_openid,p1.b2_openid FROM ".$this->keyTable." AS p1 LEFT JOIN ".$this->memberTable." AS p2 ON p2.user_openid = p1.user_openid ORDER BY p1.create_time desc";
            $sql .= " limit ".($pageNum-1).", ".$pageSize;
//            echo $sql;exit;
            return DbHelper::getInstance()->get_all($sql);
        }


        //获取线索详情
        function getOne(){
            $sql = "SELECT p1.*,p2.realname pub_realname FROM ".$this->keyTable." as p1 LEFT JOIN ".$this->memberTable." AS p2 ON p2.user_openid = p1.user_openid WHERE  p1.id = ".$this->id;
            return DbHelper::getInstance()->get_one($sql);
        }

        //根据线索查询相关发布者/购买者信息
        function getKeyDetail(){
            if($this->num == 1){
                $sql = "SELECT * FROM ".$this->memberTable." WHERE user_openid  in ('".$this->b1_openid."')";
            }else if($this->num == 2){

                $sql = "SELECT * FROM ".$this->memberTable." WHERE user_openid  in ('".$this->b1_openid.",".$this->b2_openid."')";
            }
            return DbHelper::getInstance()->get_one($sql);
        }

        //获取用户信息
        function getMember(){
            $sql = "SELECT * FROM ".$this->memberTable." WHERE user_openid = '".$this->user_openid."'";
            return DbHelper::getInstance()->get_one($sql);
        }

        //获取所有线索数量
        function getKeyNum(){
            $sql = "SELECT COUNT(id) as num FROM ".$this->keyTable;
            if(isset($this->status) && !empty($this->status)){
                $sql .= " WHERE status in (".$this->status.")";
            }
            $result = DbHelper::getInstance()->get_one($sql);

            return $result['num'];
        }

        function getUsedNum(){
            $sql = "SELECT COUNT(id) as num FROM ".$this->keyTable." WHERE status != 0";
            $result = DbHelper::getInstance()->get_one($sql);
            return $result['num'];
        }

        function getUnUsedNum(){
            $sql = "SELECT COUNT(id) as num FROM ".$this->keyTable." WHERE status = 0";
            $result = DbHelper::getInstance()->get_one($sql);
            return $result['num'];
        }

        /*
         * 根据天数获取线索数据
         * */
        function getDateData($status){
            $sql = "SELECT COUNT(id) AS num FROM ".$this->keyTable." WHERE LEFT(create_time,10) = '".$this->date."' ";

            if(isset($status)){
                if($this->in == "1"){

                    $sql .= " AND status in (".$status.")";
                }else if($this->in == "0"){

                    $sql .= " AND status not in (".$status.")";
                }
            }
//            $sql .= " GROUP BY LEFT (create_time,10) order by create_time desc";
//            echo $sql;exit;
            return DbHelper::getInstance()->get_one($sql);
        }

        /*
         * 根据周获取线索数据
         * */
        function getWeekData($status){

            $sql = "SELECT COUNT(id) AS num FROM ".$this->keyTable." WHERE create_time >'".$this->startDate."' AND create_time<= '".$this->endDate."'";
            if(isset($status)){
                if($this->in == "1"){

                    $sql .= " AND status in (".$status.")";
                }else if($this->in == "0"){

                    $sql .= " AND status not in (".$status.")";
                }
            }
            return DbHelper::getInstance()->get_one($sql);


        }

        /*
         * 根据月份获取线索数据
         * */
        function getMonthData(){
            $sql = "SELECT COUNT(id) AS num FROM ".$this->keyTable." WHERE create_time >'".$this->startDate."' AND create_time<= '".$this->endDate."'";
            if(isset($status)){
                if($this->in == "1"){

                    $sql .= " AND status in (".$status.")";
                }else if($this->in == "0"){

                    $sql .= " AND status not in (".$status.")";
                }
            }
//            echo $sql;exit;
            return DbHelper::getInstance()->get_one($sql);
        }

        /*
         * 获取晒单信息
         * */

        function getShareOrders(){
            $sql = "SELECT p1.*,p2.realname,p2.name FROM wp_cj_share_order as p1 LEFT JOIN wp_cj_member as p2 on p1.user_openid = p2.user_openid";
            if(isset($this->status)){
                $sql .= " WHERE p1.status = ".$this->status;
            }
            $sql .= " order by p1.create_time desc";
            return DbHelper::getInstance()->get_all($sql);
        }

        /*
         * 查询
         * */
        function getShareOrderDetail(){
            $sql = "SELECT p1.*,p2.name FROM wp_cj_share_order as p1 LEFT JOIN wp_cj_member as p2 ON p2.user_openid = p1.user_openid
             WHERE p1.id = ".$this->id;
            return DbHelper::getInstance()->get_one($sql);
        }

        /*
         *
         * 审核晒单
         * */
        function verifyShareOrder(){
            $data = array(
                "status"=>$this->status,
                "remark"=>$this->remark,
                "verify_time"=>$this->verify_time
            );
            return DbHelper::getInstance()->update("wp_cj_share_order",$data,"id = ".$this->id);
        }



    }

