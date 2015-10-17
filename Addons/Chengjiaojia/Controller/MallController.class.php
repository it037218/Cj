<?php
namespace Addons\Chengjiaojia\Controller;
use Addons\Chengjiaojia\Controller\BaseController;
use Addons\Chengjiaojia\Controller\MemberController;
use Addons\Chengjiaojia\Controller\LogController;
use Addons\Chengjiaojia\Controller\CreditController;
use Addons\Chengjiaojia\Controller\MessageController;
use Addons\Chengjiaojia\Model\CreditModel;
use Addons\Chengjiaojia\Model\LogModel;
use Addons\Chengjiaojia\Model\MallModel;
use Addons\Chengjiaojia\Model\MemberModel;
use Think\Model;

    class MallController extends BaseController{
        private $user_openid;

        public function __construct(){
            parent::__construct();
//                        $this->user_openid = "o0nM4t4nSz5FDTJERyVctDjrylKw";
            if(isset($_SESSION['user_openid']) && !empty($_SESSION['user_openid'])){
                $data['user_openid'] = $_SESSION['user_openid'];
            }else  if(isset($_GET['openid'])  && $_GET['openid'] != ""){
                $data['user_openid'] = $_GET['openid'];
                $data['user_openid'] = $_GET['openid'];
                $data['nickname'] = $_GET['nickname'];
                $data['headimgurl'] = $_GET['headimgurl'];
                //检查用户是否注册
                check_register($data);
                $_SESSION['user_openid'] = $data['user_openid'];
            }else{
                $data = get_wx_id();
            }
            $this->user_openid = $data['user_openid'];

            $m = new MemberModel();
            $status = $m->check($data['user_openid']);
//            if($status != 4){
//                $url = U('/addon/Chengjiaojia/Member/check_reg');
//                header("location:".$url);
//                exit;
//            }
//            $this->user_openid = "o0nM4t4nSz5FDTJERyVctDjrylKw";
        }


        //获取商品的基本信息
        private function getInfo($data){

            $m = new MallModel();
            $result = $m->getCommodityDetail($data);
            return $result;
        }

        //商城首页
        public function index(){
            $data['status'] = 1;
            $result = M("cj_commodity")->where($data)->order("list_order asc")->select();
            $this->assign("result",$result);
            $this->show(CUSTOM_TEMPLATE_PATH."/Mall/index.html");
        }

        //商品详情

        public function getDetail(){

            $data['id'] = $_GET['id'];

            //获取用户当前积分
            $con['user_openid'] = $this->user_openid;
            $credit = M('cj_member')->where($con)->getField("use_credit");
            $this->assign('credit',$credit);
            $result = $this->getInfo($data);

            //判断用户当天是否已经兑换过该商品
            $condition = array(
                "user_openid" => $this->user_openid,
                "commodity_id" => $_GET['id'],
                "create_time" => array("egt",date("Y-m-d",time()))
            );
            $status = M("cj_mall_order")->where($condition)->find();
            if(!empty($status)){
                $this->assign("status",1);
            }else{
                $this->assign("status",0);
            }

            //获取当前时间
            $time = date("H:i:s",time());
            if($time < "10:00:00"){
                $this->assign("ready",0);
            }else{
                $this->assign("ready",1);
            }
            $this->assign("result",$result);
            $this->show(CUSTOM_TEMPLATE_PATH."/Mall/commodity_detail.html");
        }

        //积分兑换
        public function exchange(){
            $m = new MallModel();
            $user_openid = $this->user_openid;
            //积分冻结
            $info['id'] = $_POST['id'];
            //获取商品详情
            $commodityInfo = $this->getInfo($info);

            $data['user_openid'] = $user_openid;
            $data['commodity_name'] = $commodityInfo['commodity_name'];
            $data['commodity_id'] = $_POST['id'];
            $data['create_time'] = date("Y-m-d H:i:s",time());
            $data['credit'] = $commodityInfo['credit'];
            $data['count'] = 1;
            $member = new MemberModel();
            $memberInfo = $member->getMemberInfo($data['user_openid']);

            //判断用户当天是否已经兑换过该商品
            $condition = array(
                "user_openid" => $user_openid,
                "commodity_id" => $_GET['id'],
                "create_time" => array("egt",date("Y-m-d",time()))
            );
            $status = M("cj_mall_order")->where($condition)->find();

            if(!empty($status)){
                echo -3;
                exit;
            }if($memberInfo['use_credit'] < $commodityInfo['credit']){
                echo -1;
                exit;
            }else if($commodityInfo['realtime_number'] <=0){
                echo -2;
                exit;
            }else{
                if($m->createOrder($data)){
                    //兑换订单创建成功
                    //变更库存
                    $m->releaseNum($_POST['id']);
                    //扣除积分
                    $credit['use_credit'] = $commodityInfo['credit'];
                    $credit['frozen_credit'] = 0;
                    $member->creditChange($credit,$user_openid);

                    //积分变化日志
                    $description = "积分兑换-".$commodityInfo['commodity_name']."</a>";
                    $credit_data = array(
                        "user_openid" => $user_openid,
                        "credit" => $commodityInfo['credit'],
                        "title" => $description,
                        "description" => $description,
                        "create_time" => date("Y-m-d H:i:s",time())
                    );
                    $credit = new CreditModel();
                    $credit -> addCreditLog($credit_data);

                    //提示消息
                    $message = array(
                        "user_openid"=>$this->user_openid,
                        "href" => $_SERVER['HTTP_REFERER'],
                        "status" => 0,
                        "title" => "您已成功兑换".$commodityInfo['commodity_name'],
                        "create_time" => date("Y-m-d H:i:s",time())
                    );
                    add_message($message);

                    //发送模板消息
                    $message = array(
                        "user_openid" => $user_openid,
                        "url"   => U('/addon/Chengjiaojia/Mall/getDetail/id/'.$_POST['id']),
                        "first" => "您已成功兑换积分",
                        "keyword1"=>"您已成功兑换".$commodityInfo['commodity_name'],
                        "keyword2" => date("Y-m-d H:i:s",time())
                    );
                    sendNoticeMessage($message);

                    //发送积分变化模板消息
                    $creditMessage = array(
                        "user_openid" => $user_openid,
                        "url"   => U('/addon/Chengjiaojia/Mall/getDetail/id/'.$_POST['id']),
                        "first" => "兑换商品成功，积分扣除",
                        "account" => $memberInfo['name'],
                        "type" => "兑换商品成功",
                        "creditChange" => "扣除",
                        "number" => $commodityInfo['credit'],
                        "creditName" => "账户积分",
                        "amount" => $memberInfo['use_credit'],
                        "remark" => "努力赚积分吧！"
                    );
                    sendKeyMessage($creditMessage);

                    //操作日志
                    $log = new LogModel();
                    $log_data['operation'] = "兑换商品成功！";
                    $log_data['create_time'] = date("Y-m-d H:i:s",time());
                    $log_data['user_openid'] = $this->user_openid;
                    add_log($log_data);

                    echo 1;
                }
                else{
                    echo 0;
                }
            }



        }

    }

?>