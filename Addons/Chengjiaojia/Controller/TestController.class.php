<?php

namespace Addons\Chengjiaojia\Controller;
use Addons\Chengjiaojia\Controller\BaseController;
use Addons\Chengjiaojia\Model\MemberModel;
use Think\Model;
use Think\Cache\Driver\Redis;

class TestController extends BaseController{
        private $user_openid;
        public function __construct(){
            parent::__construct();
//            //获取用户openid
//            $data = get_wx_id();
//            //检查用户是否注册
//            check_register($data);
            //获取用户openid
            if(isset($_SESSION['user_openid'])){
                $this->user_openid = $_SESSION['user_openid'];
            }
            if(isset($_GET['test']) && $_GET['test'] == 1){
                //超
                $this->user_openid = "o0nM4txt6C1J0s-QYOPu8utZZ__I";
                $_SESSION['user_openid'] = $this->user_openid;
            }else if(isset($_GET['test']) && $_GET['test'] == 2){
                //朱
                $this->user_openid = "o0nM4t4nSz5FDTJERyVctDjrylKw";
                $_SESSION['user_openid'] = $this->user_openid;
            }else if(isset($_GET['test']) && $_GET['test'] == 3){
                //伟
                $this->user_openid = "o0nM4tzlrljX1Co_zqJ9Z1sKYGWg";
                $_SESSION['user_openid'] = $this->user_openid;
            }else if(isset($_GET['test']) && $_GET['test'] == 4){
                //李
                $this->user_openid = "o0nM4t1lM4_rqfTIHeusQZ5Pm1Y8";
                $_SESSION['user_openid'] = $this->user_openid;
            }else if(isset($_GET['test']) && $_GET['test'] == 5){
                //橙蕉
                $this->user_openid = "o0nM4t2H1jq4bzZdCnVtYyNLoltU";
                $_SESSION['user_openid'] = $this->user_openid;
            }else if(isset($_GET['test']) && $_GET['test'] == 6){
                //季
                $this->user_openid = "o0nM4tza3aoVJ6FsMUg4BlYAQG-I";
                $_SESSION['user_openid'] = $this->user_openid;
            }
        }



        public function sendTemplate(){
            $data['user_openid'] = "o0nM4t4nSz5FDTJERyVctDjrylKw";
            $data['brand'] = "路虎捷豹";
            $data['key_id'] = '362';
            sendBrandNotice($data);

        }

        public function redisDemo(){

            $redis = new Redis();
            $result = $redis->get("news");

            foreach($result as $key=>$value){

                //判断 每条信息是否属于当前用户
                if($value['user_openid'] == $this->user_openid){
                    $result[$key]['readStatus']=1;
                }else{
                    $result[$key]['readStatus']=0;
                }

            }
            $user_openid = $this->user_openid;
            $this->assign("user_openid",$user_openid);
            $this->assign("result",$result);
            $this->show(CUSTOM_TEMPLATE_PATH."/Test/index.html");
        }


        public function setRedis(){
            $redis = new Redis();
            $news = $redis->get("news");
            $new = array(
                "user_openid"=>$_SESSION['user_openid'],
                "message"=>$_POST['message'],
                "create_time"=>date("Y-m-d H:i:s",time()),
                "status"=>"1"
            );
            $news[] = $new;

            //查询用户最后发信息时间
            $timestamp = array();
            $timestamp = $redis->get("timestamp");

            //更改用户最后发言时间
//            echo $this->user_openid;exit;
            $user_openid = $this->user_openid;
            $array = array(
                "user_openid"=>$user_openid,
                "timestamp"=>time()
            );
            $timestamp[$user_openid]=$array;

            if($redis->set("news",$news)){
                $redis->set("timestamp",$timestamp);
                echo 1;
            }else{
                echo -1;
            }
        }

        public function getMessage(){
            $redis = new Redis();
            $oldNews = $redis->get("old");
            $newNews = $redis->get("news");
            $redis->set("old",$newNews);
            $result = array_diff($oldNews,$newNews);
            if(empty($result)){
                echo -1;
            }else{
                echo json_encode($result);
            }

        }

        public function recommendList(){

            $m = new Model();
            $sql = "SELECT * FROM view_recommender";
            $arr = array();
            $result = $m->query($sql);
            foreach($result as $key=>$value){
                $sql = "SELECT nickname,realname,telephone,status FROM wp_cj_member WHERE  recommender = '".$value['recommender']."'";
                $arr[$key] = $m->query($sql);

            }
            $this->assign("result",$arr);
            $this->show(CUSTOM_TEMPLATE_PATH."/Test/recommend.html");

        }



 }