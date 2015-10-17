<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2015/6/21
 * Time: 19:24
 */

    namespace Addons\Chengjiaojia\Model;
    use Think\Model;

    class WantedModel extends Model{

        /*
         * 获取悬赏首页展示列表
         * */
        public function getAll($data){
            $condition['buy_status'] = 0;
            $condition['end_time'] = array("egt",date("Y-m-d H:i:s",time()));
            if(isset($data['city']) && !empty($data['city'])){
                $condition['city'] = array("eq",$data['city']);
                $result = M("cj_wanted")->where($condition)->select();
            }else  if(isset($data['series']) && !empty($data['series'])){
                $condition['series'] = array("eq",$data['series']);
                $result = M("cj_wanted")->where($condition)->select();
            }else if(isset($data['order']) && !empty($data['order'])){
                if($data['order'] == "time"){
                    $result =  M("cj_wanted")->where($condition)->order("create_time desc")->select();
                }else if($data['order'] == "credit"){
                    $result =  M("cj_wanted")->where($condition)->order("credit desc")->select();
                }else{
                    $result = M("cj_wanted")->where($condition)->order("key_rate desc")->select();
                }
            }else{
                $result =  M("cj_wanted")->where($condition)->select();
            }
            return $result;
        }

        //悬赏状态
        public function checkAgreeStatus($id){
            return M('cj_wanted')->where("id = ".$id)->getField("agree_status");
        }
        //是否有人投稿
        public function checkSendStatus($id){
            return M('cj_wanted')->where("id = ".$id)->getField("send_status");
        }
        //是否购买
        public function checkBuyStatus($id){
            return M('cj_wanted')->where("id = ".$id)->getField("buy_status");
        }



        /*
         *  获取悬赏线索详情
         * */
        public function getDetail($id){
            $result = M("cj_wanted")->where("id = ".$id)->find();
            return $result;
        }

        /*
         * 用户获取悬赏列表
         * */
        public function getWantedList($status,$user_openid){
            $result['total'] = M("cj_wanted")->where($status)->where($user_openid)->count('id');
            $result['rows'] = M("cj_wanted")->where($status)->where($user_openid)->select();
            return $result;
        }

        /*
         * 保存悬赏信息
         * */
        public  function save($data){
            $result = M("cj_wanted")->add($data);
            return $result;
        }

        /*
         * 保存悬赏信息
         * */
        public function update($id,$data){
            return M('cj_wanted')->where("id = ".$id)->save($data);
        }

        /*
         * 查询每条悬赏对应的揭榜线索列表
         * */

        public function keyWantedList($id){
            return M("cj_wanted_get")->where("wanted_id = ".$id)->select();
        }


        /*
         * 查询悬赏发布者
         * */

        public function checkMember($id){
            return M("cj_wanted")->where("id = ".$id)->getField("user_openid");
        }

        /*
         * 保存用户揭榜记录
         * */
        public function saveWantedGet($data){
            return M("cj_wanted_get")->add($data);
        }

        /*
         *判断线索状态
         *  */
        public function checkKeyStatus($id){
            return M("cj_key")->where("id = ".$id)->getField("status");
        }

        /*
         * 查看悬赏发布者信息
         * */

        public function getWantedSender($user_openid){
            return M("cj_member")->where("user_openid = '".$user_openid."'")->find();
        }

        /*
         *
         * 获取用户揭榜的悬赏列表
         * */

        public function getJieWantedList1($status,$user_openid){
            $sql = "SELECT p1.* ,p2.* FROM wp_cj_wanted_get as p1
            LEFT JOIN wp_cj_wanted as p2 ON p1.wanted_id=p2.id
            WHERE p1.get_openid='".$user_openid."' AND p2.agree_status = ".$status;

            $totalSql = "SELECT count(p1.id) as total FROM wp_cj_wanted_get as p1
            LEFT JOIN wp_cj_wanted as p2 ON p1.wanted_id=p2.id
            WHERE p1.get_openid='".$user_openid."' AND p2.agree_status = ".$status;

            if($status == 0){
                $sql .=" AND p2.end_time>='".date("Y-m-d H:i:s",time())."'";
                $totalSql .=" AND p2.end_time>='".date("Y-m-d H:i:s",time())."'";
            }else{
                $sql .=" AND p2.end_time<'".date("Y-m-d H:i:s",time())."'";
                $totalSql .=" AND p2.end_time<'".date("Y-m-d H:i:s",time())."'";
            }
            $sql .= "GROUP BY	p1.wanted_id";
            $totalSql .="GROUP BY	p1.wanted_id";
//            echo $sql;exit;
            $total = M()->query($totalSql);
            $result['total'] = $total[0]['total'];
            $result['rows'] =M()->query($sql);

            return $result;

        }

        /*
         * 获取用户揭榜线索列表
         * */
        public function getJieWantedList($status,$user_openid){
            if($status == 0){
                $where['wp_cj_wanted_get.status'] = array("eq",0);
            }else{
                $where['wp_cj_wanted_get.status'] = array("gt",0);
            }
            $where['wp_cj_wanted_get.status'] = $status;

            $m = M("cj_key");
            $result['rows'] = $m->join("wp_cj_wanted_get ON wp_cj_key.id = wp_cj_wanted_get.key_id")
                ->where("wp_cj_key.is_wanted = 1")->where($where)
                ->where("wp_cj_key.user_openid ='".$user_openid."'")->field("wp_cj_key.*")->select();

            $result['total'] = $m->join("wp_cj_wanted_get ON wp_cj_key.id = wp_cj_wanted_get.key_id")
                ->where("wp_cj_key.is_wanted = 1")->where($where)
                ->where("wp_cj_key.user_openid ='".$user_openid."'")->count();
            return $result;
        }

        //获取用户每条揭榜悬赏所对应的线索信息
        public function getKeyWantedList($id,$user_openid){
            $m = M("cj_key");
            $condition['wp_cj_wanted_get.wanted_id'] = $id;
            $condition['wp_cj_key.user_openid'] = $user_openid;
//            $condition['wp_cj_key.is_wanted'] = 1;
            return $m->join("wp_cj_wanted_get ON wp_cj_key.id = wp_cj_wanted_get.key_id")
                    ->where($condition)->field("wp_cj_key.*")->select();
        }

        //更改积分
        public function changeCredit($credit,$user_openid){
        }

        //查询同一线索是否已近揭过同一悬赏‘
        public function checkJieBang($wanted_id,$key_id){
            $data['wanted_id'] = $wanted_id;
            $data['key_id'] = $key_id;
            return M("cj_wanted_get")->where($data)->find();
        }


    }

