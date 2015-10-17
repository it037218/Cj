<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 15/9/14
 * Time: 上午2:23
 */

    class Member {
        var $memberTable = "wp_cj_member";
        var $verifyTable = "wp_cj_verify";
        var $status;
        var $type;
        var $telephone;
        var $password;
        var $user_openid;
        var $user_id;
        var $verify_result;
        var $verify_time;
        var $city_id;
        var $realname;
        var $name;

        function getMemberList($pageNum,$pageSize){
            $sql = "SELECT p1.*,p2.brand as brand FROM  ".$this->memberTable." as p1 left join wp_cj_brand as p2 ON p2.id = p1.brand_id WHERE 1=1 ";
            $totalSql = "SELECT COUNT(id) AS num FROM ".$this->memberTable." WHERE 1=1";
            if(isset($this->status)){
                $sql .=" AND p1.status = ".$this->status;
                $totalSql .= " AND status = ".$this->status;
            }
            if(isset($this->telephone) && !empty($this->telephone)){
                $sql .= " AND p1.telephone like '%".$this->telephone."%'";
                $totalSql .= " AND telephone like '%".$this->telephone."%'";
            }
            if(isset($this->name) && !empty($this->name)){
                $sql .= " AND p1.name like '%".$this->name."%'";
                $totalSql .= " AND name like '%".$this->name."%'";
            }
            if(isset($this->city_id) && !empty($this->city_id)){
                $sql .= " AND p1.city_id = ".$this->city_id;
                $totalSql .= " AND city_id = ".$this->city_id;
            }

            $sql .= " order by p1.create_time desc limit ".($pageNum-1)*$pageSize.", ".$pageSize;
            $result['rows'] = DbHelper::getInstance()->get_all($sql);
            $rst = DbHelper::getInstance()->get_one($totalSql);
            $result['num'] = $rst['num'];
            return $result;
        }

        function getMemberNum(){
            $sql = "SELECT COUNT(id) AS num FROM ".$this->memberTable;
            if(isset($this->status) && !empty($this->status)){
                $sql .=" WHERE status = ".$this->status;
            }
            return DbHelper::getInstance()->get_one($sql);
        }


        function checkLogin(){
            $sql = "SELECT * FROM ".$this->memberTable." WHERE telephone = ".$this->telephone." AND pwd = '".$this->password."' AND type = ".$this->type;
            return DbHelper::getInstance()->get_one($sql);
        }

        /*
         * 获取近期审核记录
         * */

        function getRecentVerify(){
            $sql = "SELECT * FROM ".$this->verifyTable."  order by verify_time desc ";
            return DbHelper::getInstance()->get_all($sql);
        }

        /*
         * 对用户进行审核
         * */
        function verify(){
            $data['status'] = $this->status;
            $data['verify_time'] = $this->verify_time;
            return DbHelper::getInstance()->update($this->memberTable,$data,"id = ".$this->user_id);
        }

        /*
         * 根据用户编号查询用户信息
         * */
        function getMemberDeatail(){
            $sql = "SELECT * FROM ".$this->memberTable." WHERE id = ".$this->user_id;
            return DbHelper::getInstance()->get_one($sql);
        }

        /*
         * 添加审核记录
         * */
        function addVerifyLog($data){
            return DbHelper::getInstance()->insert("wp_cj_verify",$data);
        }

        /*
         * 获取用户编辑审核
         * */
        function getEditVerify(){
            $sql = "SELECT p2.nickname,p2.avatar,p2.telephone,p1.* from wp_cj_info_edit as p1 LEFT  JOIN wp_cj_member as p2 ON p1.user_openid = p2.user_openid order by p1.create_time DESC ";
            return DbHelper::getInstance()->get_all($sql);
        }

        /*
         * 获取用户提交编辑资料明细
         * */

        function getEditInfo(){
            $sql = "SELECT p1.*,p2.telephone,p2.nickname FROM wp_cj_info_edit as p1 LEFT JOIN wp_cj_member as p2 ON p1.user_openid = p2.user_openid WHERE  p1.id = ".$this->user_id;
            return DbHelper::getInstance()->get_one($sql);
        }

        /*
         * 修改提交编辑资料状态
         *
         * */
        function editVerify(){
            $data = array(
                "status"=>$this->status,
            );

            return DbHelper::getInstance()->update("wp_cj_info_edit",$data,"id = ".$this->user_id);
        }
        /*
         * 修改用户信息状态
         *
         * */
        function changeStatus(){
            $data['realStatus'] = 1;
            $data['verify_time'] = $this->verify_time;
            return DbHelper::getInstance()->update($this->memberTable,$data,"user_openid = '".$this->user_openid."'");
        }
        /*
         * 审核成功时，更新用户信息
         * */

        function updateInfo($data){
            return DbHelper::getInstance()->update($this->memberTable,$data,"user_openid = '".$this->user_openid."'");
        }


    }
