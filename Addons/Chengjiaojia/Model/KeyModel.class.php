<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2015/6/11
 * Time: 6:42
 */
    namespace Addons\Chengjiaojia\Model;
    use Think\Model;


    class KeyModel extends Model{

        var $status;
        var $id ;
        //检查线索的状态
        public function checkStatus($id){
            $result =  M("cj_key")->where("id = ".$id)->getField("status");
            return $result;
        }
        //查看线索发布者
        public function checkMember($id){
            return M("cj_key")->where("id = ".$id)->getField("user_openid");
        }


        //线索池中的线索列表
        public function  getIndexKeysList($data){
            $condition['wp_cj_key.status'] =$data['status'];
            $condition['wp_cj_key.is_wanted'] = 0;
            if(isset($data['hide'])){
                $condition['wp_cj_key.hide'] = $data['hide'];
            }
//            $condition['wp_cj_key.b1_openid'] = array("neq",$data['user_openid']);
//            $condition['wp_cj_key.b2_openid'] = array("neq",$data['user_openid']);
            if(isset($data['city']) && !empty($data['city'])){
                $condition['wp_cj_key.city'] = array("eq",$data['city']);

                $result =  M("cj_key")->field("wp_cj_key.*,wp_cj_member.bought_keys,wp_cj_member.useful_keys")->JOIN("wp_cj_member ON wp_cj_member.user_openid = wp_cj_key.user_openid")->where($condition)->order("wp_cj_key.create_time desc")->select();
            }else  if(isset($data['brand']) && !empty($data['brand'])){
                $condition['wp_cj_key.brand'] = array("eq",$data['brand']);
                $result =  M("cj_key")->field("wp_cj_key.*,wp_cj_member.bought_keys,wp_cj_member.useful_keys")->JOIN("wp_cj_member ON wp_cj_member.user_openid = wp_cj_key.user_openid")->where($condition)->select();
            }else if(isset($data['order']) && !empty($data['order'])){
                if($data['order'] == "time"){
                    $result =  M("cj_key")->field("wp_cj_key.*,wp_cj_member.bought_keys,wp_cj_member.useful_keys")->JOIN("wp_cj_member ON wp_cj_member.user_openid = wp_cj_key.user_openid")->where($condition)->order("wp_cj_key.create_time desc")->select();
                }else if($data['order'] == "credit"){
                    $result =  M("cj_key")->field("wp_cj_key.*,wp_cj_member.bought_keys,wp_cj_member.useful_keys")->JOIN("wp_cj_member ON wp_cj_member.user_openid = wp_cj_key.user_openid")->where($condition)->order("wp_cj_key.credit desc")->select();
                }else{
                    $result =  M("cj_key")->field("wp_cj_key.*,wp_cj_member.bought_keys,wp_cj_member.useful_keys")->JOIN("wp_cj_member ON wp_cj_member.user_openid = wp_cj_key.user_openid")->where($condition)->order("wp_cj_member.bought_keys/wp_cj_member.useful_keys desc")->select();
                }
            }else{
                $result =  M("cj_key")->field("wp_cj_key.*,wp_cj_member.bought_keys,wp_cj_member.useful_keys")->JOIN("wp_cj_member ON wp_cj_member.user_openid = wp_cj_key.user_openid")->where($condition)->order("wp_cj_key.create_time desc")->select();
            }
            return $result;
        }
        //查找已被购买的线索



        //根据状态查询线索
        public  function  getListsByStatus($status){
            $result = M("cj_key")->where("status = ".$status)->find();
            return $result;
        }

        /*
         * 获取线索详情
         * 从首页列表进入
         * */
        public function getKeyDetail($id){
            return M("cj_key")->where("id = ".$id)->find();
        }

        //发布线索
        public function publishKey($data){
            $result = M("cj_key")->data($data)->add();
            return $result;
        }

        //获取用户发布线索的总条数
        public function getUserKeys($data){
            $result['keysTotal'] = M("cj_key")->where("user_openid = ".$data)->select();
            $result['num'] = M("cj_key")->where("user_openid = ".$data) -> count();
            return $result;
        }

        //获取用户发布有效线索的总条数
        public function getUsedKeys($data){
            $result['keysUsed'] = M("cj_key")->where("user_openid = ".$data." and status >0")->select();
            $result['num'] = M("cj_key")->where("user_openid = ".$data." and status >0")->count();
            return $result;
        }

        //获取用户所有线索中，被判为有效的线索数
        public function getUsefulKeys($data){
            $result['keysUseful'] = M("cj_key")->where("user_openid = ".$data." and status >0")->select();
            $result['num'] = M("cj_key")->where("user_openid = ".$data." and status >0")->count();
            return $result;
        }

        //获取用户发布的线索
        public function getKeyList($status,$user_openid){
            $result['total'] = M("cj_key")->where($status)->where($user_openid)->count('id');
            $result['rows'] = M("cj_key")->where($status)->where($user_openid)->order("create_time desc")->select();
            return $result;
        }

        //获取用户已发布的线索（未完成）
        public function keyRepositories($user){
            //
            return M("cj_key")->where("status < 10 and user_openid = '".$user."'")->select();

        }


        /*
         * 改变线索状态
         *
         * 购买线索
         * 判断线索 有效/无效
         *
         * */
        public function changeStatus($id,$data){
            return M("cj_key")->data($data)->where("id = ".$id) ->save();
        }

        /*
         * 获取用户已购买的线索
         * 已验证/未验证
         * */
        public function getBoughtKey($status,$user_openid){

            $m = M("cj_key_bought");
            $result['rows'] = $m->join("wp_cj_key ON wp_cj_key_bought.key_id = wp_cj_key.id")->order("wp_cj_key.create_time desc")
                ->where("wp_cj_key_bought.user_openid = '".$user_openid."'")
                ->where($status)->field("wp_cj_key.brand,wp_cj_key.brand_logo,wp_cj_key.brand_id,wp_cj_key.series,wp_cj_key.time_limit,wp_cj_key.create_time,wp_cj_key.id,wp_cj_key_bought.status ")->select();
            $result['total'] =$m->join("wp_cj_key ON wp_cj_key_bought.key_id = wp_cj_key.id")
                ->where("wp_cj_key_bought.user_openid = '".$user_openid."'")
                ->where($status)->count();
            return $result;
        }


        /*
         * 添加用户评价记录
         *
         * */
        public function addJudgeLog($data){
            return M("cj_judge")->add($data);
        }

        /*
         * 获取用户发布的线索列表
         * */
        public function getPublishKeyList($status,$user_openid){
            return ;
        }

        /*
         * 获取用户可使用的线索
         * status 小于 10
         * */
        public function getAvailableKeys($user_openid){
            return M("cj_key")->where("user_openid = '".$user_openid."")->where("status < 10 and is_wanted = 0")->select();
        }

        /*
         * 修改线索内容
         *
         * */
        public function update($id,$data){
            return M("cj_key")->where("id = ".$id)->save($data);

        }

        /*
         * 查询用户购买线索记录
         * */
        public function getKeyBought($condition){
            return M("cj_key_bought")->where($condition)->find();
        }

        /*
         * 更新用户购买线索的状态
         * */
        public function updateBoughtKey($data,$status){
            $res['status'] = $status;
            return M("cj_key_bought")->where($data)->save($res);
        }

        //获取线索评价详情
        public function getJudge($data){
            return M("cj_judge")->where($data)->find();
        }


        /*
         * 积分变化
         * */
        public function changeCredit($data,$user_openid){
            M('cj_member')->where("user_openid ='".$user_openid."'")->setInc("use_credit",$data['use_credit']);
            M('cj_member')->where("user_openid ='".$user_openid."'")->setInc("frozen_credit",$data['frozen_credit']);

        }

    }