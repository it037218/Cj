<?php

    namespace Addons\Chengjiaojia\Controller;
    use Addons\Chengjiaojia\Controller\BaseController;
    use Addons\Chengjiaojia\Model\KeyModel;
    use Addons\Chengjiaojia\Controller\PublicController;
    use Addons\Chengjiaojia\Model\MemberModel;
    use Addons\Chengjiaojia\Model\CreditModel;
    use Addons\Chengjiaojia\Model\LogModel;
    use Addons\Chengjiaojia\Model\WantedModel;
    use Think\Model;
    use \Org\Util;

    class KeyController extends BaseController{
        private $user_openid;
        public function __construct(){
            parent::__construct();
           //判断用户是否已经注册
            //获取用户openid
            if(isset($_GET['test']) && $_GET['test'] == 1){
                $this->user_openid = "o0nM4txt6C1J0s-QYOPu8utZZ__I";
                $_SESSION['user_openid'] = $this->user_openid;
            }else if(isset($_GET['test']) && $_GET['test'] == 2){
                $this->user_openid = "o0nM4t4nSz5FDTJERyVctDjrylKw";
                $_SESSION['user_openid'] = $this->user_openid;
            }else if(isset($_GET['test']) && $_GET['test'] == 3){
                $this->user_openid = "o0nM4tzlrljX1Co_zqJ9Z1sKYGWg";
                $_SESSION['user_openid'] = $this->user_openid;
            }
            else{
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
                $con['user_openid'] = $this->user_openid;
                $realStatus = M("cj_member")->where($con)->getField("realStatus");
                if($status < 4 && $realStatus <1){
                    $url = U('/addon/Chengjiaojia/Member/check_reg');
                    header("location:".$url);
                    exit;
                }
            }
        }
        //获取线索详情
        private function getKeyInfo($id){
            $key = new KeyModel();
            return $key -> getKeyDetail($id);
        }
        //检查线索状态
        private function checkStatus($id){
            $m = new KeyModel();
            return $m ->checkStatus($id);
        }

        //检查线索发布者
        private function checkMember($id){
            $m = new KeyModel();
            return $m ->checkMember($id);
        }
        //获取用户信息
        private function getMember($user_openid){
            $m = new MemberModel();
            return $m->getOne($user_openid);
        }
        public function getKey($id){
            $data['id'] = $id;
            return M("cj_key")->where($data)->find();
        }
        /*
         *线索列表页
         * 首页
         * */
    	public function index(){
            $m = new KeyModel();
            $data=array();
//            if(isset($_GET['city']) && !empty($_GET['city'])){
//                $data['city'] = $_GET['city'];
//                $result = $m->getIndexKeysList($data);
//            }else if(isset($_GET['series']) && !empty($_GET['series'])){
//                $data['series'] = $_GET['series'];
//                $result = $m->getIndexKeysList($data);
//            }else if(isset($_GET['order']) && !empty($_GET['order'])){
//                $data['order'] = $_GET['order'];
//                $result = $m->getIndexKeysList($data);
//            }else{
//
//            }


//            $result = M("cj_key")->select();

            //线索列表页，只能查看用户自身的品牌系列，自己城市的线索，并且是之前并没有买过的线索
            //获取用户信息
            $user_openid = $this->user_openid;
            $userInfo = $this->getMember($user_openid);
            $data['city'] = $userInfo['city'];
            $data['brand'] = $userInfo['brand'];
            $data['b1_openid'] = array('neq',$user_openid);
            $data['b2_openid'] = array('neq',$user_openid);
            $data['user_openid'] = $user_openid;

            /*
             * 有效线索展示
             * */
            $data['status'] = array("between","0, 10");
            $data['hide'] = 0;
            $result = $m->getIndexKeysList($data);
            foreach($result as $k=>$v){
                $v['bought_keys'] = $v['bought_keys']==0?1:$v['bought_keys'];
                $result[$k]['key_rate'] = $v['useful_keys']/$v['bought_keys']*5;

                $users = array( $v['b1_openid'],$v['b2_openid'],$v['b3_openid']);
            }
            $this->assign("result",$result);


            /*
             * 已被抢的线索展示
             * */
            $data['status'] = array("not in","0,10");
            $rst = $m->getIndexKeysList($data);
            foreach($rst as $k=>$v){
                $v['bought_keys'] = $v['bought_keys']==0?1:$v['bought_keys'];
                $rst[$k]['key_rate'] = $v['useful_keys']/$v['bought_keys']*5;
            }

            $this->assign("rst",$rst);
            $brand = $this->getMember($this->user_openid);
            $this->assign("brand",$brand['brand']);
    		$this->show(CUSTOM_TEMPLATE_PATH."/Key/index.html");
    	}

        /*
        *   购买时
        *   查看线索详情
        *   购买者角度
        *   状态：查看状态；已购买/未购买
        */
        public function getKeyDetail(){
            //当用户未购买线索时
            $m = new KeyModel();
            $id = $_GET['id'];
            //查询该条线索的状态
            $status = $this -> checkStatus($id);
            if($status >=0 && $status <10 ){
                //未购买线索
                $this->getUnBoughtKey($id);
            }else{
                //已购买 未验证
                $this->getBoughtKey($id);
            }
//        else if($status>=100 && $status <999){
//                //验证有效的线索
//                $this->getKeyDetailDetail($id,$status);
//            }else if($status == 999){
//                echo "验证无效的线索";
//            }
        }

        /*
         * 获取用户已经评价的线索详情
         * 有效线索，无效线索
         * */
        public function getVerifiedKeyDetail($data){
            $id = isset($data)?$data:$_GET['id'];
            //查询购买记录表，判断该线索的评价记录
            $key = new KeyModel();
            $condition['key_id'] = $id;
            $condition['user_openid'] = $this->user_openid;
            $info = $key->getKeyBought($condition);
            //获取线索详情
            $result = $key->getKeyDetail($id);
            $this->assign("result",$result);

            //获取相关评价记录
            $judge['judge_openid'] = $this->user_openid;
            $judge['key_id'] = $id;
            $judgeInfo = $key->getJudge($judge);

            $this->assign("judgeInfo",$judgeInfo);

            //获取线索发布者的信息
            $user_openid = $result['user_openid'];
            $user_info = $this->getMember($user_openid);
            $this->assign("user_info",$user_info);

            //获取发布者信用
            $rate = $user_info['useful_keys']/$user_info['bought_keys']*5;
            $this->assign("rate",$rate);

            if($info['status'] == 1){
                $this->show(CUSTOM_TEMPLATE_PATH."/Key/verifiedTrueKey.html");
            }else{
                $this->show(CUSTOM_TEMPLATE_PATH."/Key/verifiedFalseKey.html");
            }
        }


        /*
         * 线索详情
         * 购买者角度
         * 未购买状态
         * */
        public function getUnBoughtKey($id){
            $result = $this->getKeyInfo($id);
            $user_openid = $result['user_openid'];
            if($user_openid == $this->user_openid){
                $self = 1;
            }else{
                $self = 0;
            }
            $this->assign("self",$self);
            $user_info = $this->getMember($user_openid);
            $this->assign("user_info",$user_info);
            $this->assign("result",$result);
            $rate = $user_info['useful_keys']/$user_info['bought_keys']*5;
            $this->assign("rate",$rate);
            $openid = $this->user_openid;
            $res = M("cj_member")->where("user_openid='".$openid."'")->find();
//            $this->assign("credit",$res['use_credit']);
            $this->show(CUSTOM_TEMPLATE_PATH."/Key/unBoughtKey.html");
        }
        /*
         * 线索详情
         * 已购买但未验证
         * 购买者角度
         * */
        public function getBoughtKey($id){
            $result = $this->getKeyInfo($id);
//            $user_openid = $result['user_openid'];
            //判断当前线索的状态
            $status = $this->checkStatus($id);
            $checkStatus = "";
            if($status == 11){
                $checkStatus = $result['b1_openid'];
            }else if($status == 12){
                $checkStatus = $result['b2_openid'];
            }else if($status == 13){
                $checkStatus = $result['b3_openid'];
            }

            if($this->user_openid != $checkStatus){
                echo "<meta http-equiv='Content-Type'' content='text/html; charset=utf-8'>";

                echo "<script>alert('该线索已被购买！');history.go(-1)</script>";
                exit;
            }else{
                $user_openid = $result['user_openid'];
            }

            $user_info = $this->getMember($user_openid);
            $rate = $user_info['useful_keys']/$user_info['bought_keys']*5;
            $this->assign("rate",$rate);
            $this->assign("user_info",$user_info);
            $this->assign("result",$result);
            $this->assign("now",time());
            $this->assign("id",$id);
            $this->show(CUSTOM_TEMPLATE_PATH."/Key/boughtKey.html");
        }
        /*
         * 查看线索
         * 已验证（有效/无效）
         * 购买者角度
         * */
        public function getVerifiedKey($id,$status){
            $result = $this->getKeyInfo($id);
            $this->assign("result",$result);
            $this->show(CUSTOM_TEMPLATE_PATH."/Key/verifiedKey.html");
        }

        /*
         * 购买线索
         * id status
         * */
//        public function buyKey_bak(){
//            $id = $_POST['id'];
//            $status = $this->checkStatus($id);
//            $member = $this->checkMember($id);
//            $user_openid = $this->user_openid;
//            //获取购买者信息
//            $m = new MemberModel();
//            $user = $m->getOne($user_openid);
//
//            //获取线索详情
//            $keyInfo = $this->getKeyInfo($id);
//            //获取该条线索下的所有可能购买者
//            $buyer_openid = M("cj_key")->where("id = ".$id)->field("b1_openid,b2_openid,b3_openid")->find();
//            if($user_openid == $member){
//                //发布者和购买者为同一人
//                echo -1;
//                exit;
//            }else if($status >10){
//                //线索状态不对
//                echo -2;
//                exit;
//            }else if($user['use_credit'] < $keyInfo['credit']) {
//                echo -3;
//                exit;
//            }elseif(in_array($user,$buyer_openid)){
//                //同一用户不能重复购买同一线索
//                echo -4;
//                exit;
//            }{
//                //更新线索状态
//                $data = array();
//                if($status['status'] == 0){
//                    //刚发布的线索
//                    //购买者为b1
//                    $data['status'] = 11;
//                    $data['b1_openid'] = $user_openid;
//                    $data['b1_buy_time'] = date("Y-m-d H:i:s",time());
//                    $data['b1_telephone'] = $user['telephone'];
//                }else if($status['status'] == 1){
//                    //b1判为无效的线索
//                    //购买者为b2
//                    $data['status'] = 12;
//                    $data['b2_openid'] = $user_openid;
//                    $data['b2_buy_time'] = date("Y-m-d H:i:s",time());
//                    $data['b2_telephone'] = $user['telephone'];
//                }else if($status['status'] == 2){
//                    //b2判断为无效   b3购买
//                    $data['status'] = 13;
//                    $data['b3_openid'] = $user_openid;
//                    $data['b3_buy_time'] = date("Y-m-d H:i:s",time());
//                    $data['b3_telephone'] = $user['telephone'];
//                }
//                //获取该线索积分
//                $credit= $keyInfo['credit'];
//
//                //购买者积分冻结
//                $condition['user_openid'] = $user_openid;
//                $data['use_credit'] = -$credit;
//                $data['frozen_credit'] = -$credit;
//                changeCredit($condition,$data);
//
//                $key = new KeyModel();
//                if($key -> changeStatus($id,$data)){
//                    //添加购买记录
//                    $res ['user_openid'] = $user_openid;
//                    $res['key_id'] = $id;
//                    $res['status'] = 0;
//                    $res['create_time'] = date("Y-m-d H:i:s",time());
//                    $this->addKeyBought($res);
//
//                    //给购买者添加基本明细记录
//                    $description = "您购买了一条线索,冻结积分";
//                    $credit_data = array(
//                        "user_openid" => $user_openid,
//                        "credit" => "-10",
//                        "title" => $description,
//                        "description" => $description,
//                        "create_time" => date("Y-m-d H:i:s",time())
//                    );
//                    addCreditLog($credit_data);
//
//                    //给购买者发送系统消息
//                    $sysMsg1 = array(
//                        "user_openid" => $user_openid,
//                        "href"   => U("/addon/Chengjiaojia/Key/getUserBoughtKey/id/".$id),
//                        "title" => "您已购买一条线索",
//                        "status"=> 0,
//                        "create_time" => date("Y-m-d H:i:s",time())
//                    );
//                    //给线索发布者发送系统消息
//                    $sysMsg2 = array(
//                        "user_openid" => $member,
//                        "href"   => U("/addon/Chengjiaojia/Key/publishKeyDetail/id/".$id),
//                        "title" => "您有一条线索被购买",
//                        "status"=> 0,
//                        "create_time" => date("Y-m-d H:i:s",time())
//                    );
//
//                    addSysMsg($sysMsg1);
//                    addSysMsg($sysMsg2);
//
//                    //给购买者发送模板消息
//                    $message1=array(
//                        "user_openid" =>$user_openid,
//                        "url"   => U("/addon/Chengjiaojia/Key/getUserBoughtKey/id/".$id),
//                        "first" =>"购买线索消息提示",
//                        "keyword1" => "您已购买了一条线索",
//                        "keyword2" => date("Y-m-d H:i:s",time())
//                    );
//                    sendNoticeMessage($message1);
//
//                    //给发布者发送模板消息
//                    $message2=array(
//                        "user_openid" => $member,
//                        "url"   => U("/addon/Chengjiaojia/Key/publishKeyDetail/id/".$id),
//                        "first" =>"线索消息提示",
//                        "keyword1" => "您有一条线索被购买",
//                        "keyword2" => date("Y-m-d H:i:s",time())
//                    );
//                    sendNoticeMessage($message2);
//
//                    //线索发布者被购买数增加
//                    M("cj_member")->where("user_openid = '".$member."'")->setInc("bought_keys");
//                    echo 1;
//                }else{
//                    echo -3;
//                }
//            }
//        }

        /*
         * 购买线索
         * 橙蕉豆
         * */
        public function buyKey(){
            $id = $_POST['id'];
            //校验用户输入的交易密码是否正确
            $pwd = $_POST['pwd'];
            $result = $this->checkPwd($pwd);
            if($result == false){
                echo -10;
                exit;
            }else{
                //检查线索状态
                $status = $this->checkStatus($id);
                //查找线索发布者openid
                $member = $this->checkMember($id);

                //获取购买者信息
                $m = new MemberModel();
                $user_openid = $this->user_openid;
                $user = $m->getOne($user_openid);

                //获取线索详情
                $keyInfo = $this->getKeyInfo($id);
                //获取该条线索下的所有可能购买者
                $buyer_openid = M("cj_key")->where("id = ".$id)->field("b1_openid,b2_openid,b3_openid")->find();

                if($user_openid == $member){
                    //发布者和购买者为同一人
                    echo -1;
                    exit;
                }else if($status >10){
                    //线索状态不对
                    echo -2;
                    exit;
                }else if(($user['use_account']+200) < $keyInfo['money']) {
                    //检查用户的可用橙蕉豆是否足够购买该条线索
                    echo -3;
                    exit;
                }elseif(in_array($user,$buyer_openid)){
                    //同一用户不能重复购买同一线索
                    echo -4;
                    exit;
                }else{
                    //更新线索状态
                    $data = array();
                    if($status['status'] == 0){
                        //刚发布的线索
                        //购买者为b1
                        $data['status'] = 11;
                        $data['b1_openid'] = $user_openid;
                        $data['b1_buy_time'] = date("Y-m-d H:i:s",time());
                        $data['b1_telephone'] = $user['telephone'];
                    }else if($status['status'] == 1){
                        //b1判为无效的线索
                        //购买者为b2
                        $data['status'] = 12;
                        $data['b2_openid'] = $user_openid;
                        $data['b2_buy_time'] = date("Y-m-d H:i:s",time());
                        $data['b2_telephone'] = $user['telephone'];
                    }else if($status['status'] == 2){
                        //b2判断为无效   b3购买
                        $data['status'] = 13;
                        $data['b3_openid'] = $user_openid;
                        $data['b3_buy_time'] = date("Y-m-d H:i:s",time());
                        $data['b3_telephone'] = $user['telephone'];
                    }
                    //获取该线索积分
                    $money= $keyInfo['money'];

                    //购买者积分冻结
                    $condition['user_openid'] = $user_openid;
                    $data['use_account'] = -$money;
                    $data['frozen_account'] = -$money;
                    changeAccount($condition,$data);

                    $key = new KeyModel();
                    if($key -> changeStatus($id,$data)){
                        //添加购买记录
                        $res ['user_openid'] = $user_openid;
                        $res['key_id'] = $id;
                        $res['status'] = 0;
                        $res['create_time'] = date("Y-m-d H:i:s",time());
                        $this->addKeyBought($res);

                        //给购买者添加基本明细记录
                        $description = "您购买了一条线索,冻结橙蕉豆";
                        $account_data = array(
                            "user_openid" => $user_openid,
                            "remark" => "-".$money,
                            "account"=> $money,
                            "desc" => $description,
                            "status" => 1,
                            "type" => 3,        //购买线索
                            "create_time" => date("Y-m-d H:i:s",time())
                        );
                        addAccountLog($account_data);

                        //给购买者发送系统消息
                        $sysMsg1 = array(
                            "user_openid" => $user_openid,
                            "href"   => U("/addon/Chengjiaojia/Key/getUserBoughtKey/id/".$id),
                            "title" => "您已购买一条线索",
                            "status"=> 0,
                            "create_time" => date("Y-m-d H:i:s",time())
                        );
                        //给线索发布者发送系统消息
                        $sysMsg2 = array(
                            "user_openid" => $member,
                            "href"   => U("/addon/Chengjiaojia/Key/publishKeyDetail/id/".$id),
                            "title" => "您有一条线索被购买",
                            "status"=> 0,
                            "create_time" => date("Y-m-d H:i:s",time())
                        );

                        addSysMsg($sysMsg1);
                        addSysMsg($sysMsg2);

                        //给购买者发送模板消息
                        $message1=array(
                            "user_openid" =>$user_openid,
                            "url"   => U("/addon/Chengjiaojia/Key/getUserBoughtKey/id/".$id),
                            "first" =>"购买线索消息提示",
                            "keyword1" => "您已购买了一条线索",
                            "keyword2" => date("Y-m-d H:i:s",time())
                        );
                        sendNoticeMessage($message1);

                        //给发布者发送模板消息
                        $message2=array(
                            "user_openid" => $member,
                            "url"   => U("/addon/Chengjiaojia/Key/publishKeyDetail/id/".$id),
                            "first" =>"线索消息提示",
                            "keyword1" => "您有一条线索被购买",
                            "keyword2" => date("Y-m-d H:i:s",time())
                        );
                        sendNoticeMessage($message2);

                        //线索发布者被购买数增加
                        M("cj_member")->where("user_openid = '".$member."'")->setInc("bought_keys");
                        echo 1;
                    }else{
                        echo -3;
                    }
                }
            }
        }


        /*
         * 进入线索发布页
         * */
        public function publishKey(){
            $m = new MemberModel();
            /*
             * 需要查询的字段
             * 查询用户积分
             * */
            $data = array("credit");
            $user_openid = $this->user_openid;
            $result  = $m -> getMemberInfo($user_openid);
            $this->assign("result",$result);
            $this->show(CUSTOM_TEMPLATE_PATH."/Key/publish.html");
        }

    	/*
    	 * 保存线索
    	 * */
    	public function publish()
    	{
            $m = new MemberModel();
            //获取用户信息
            $user_openid = $this->user_openid;

            //判断用户积分情况
            $key = new KeyModel();
            //获取线索配置信息
            $data = array(
                "user_openid" => $user_openid,              //用户编号
                "province"  => trim($_POST['province']),          //线索所属省
                "city"  => trim($_POST['city']),                  //线索所属市
                "district" => trim($_POST['district']),         //所属区县
                "area"=> trim($_POST['area']),
                "brand" => trim($_POST['brand']),                 //线索所属品牌
                "brand_id" => trim($_POST['brand_id']),                 //线索所属品牌编号
                "series" => trim($_POST['series']),               //线索所属系列
                "time_limit" => trim($_POST['time_limit']),       //线索时间限制
                "custom_name" => trim($_POST['custom_name']),     //客户姓名
                "custom_telephone" => trim($_POST['custom_telephone']),   //客户电话
//                "credit" => trim($_POST['credit']),               //线索积分
                "money" => trim($_POST['money']),
                "brand_logo" => trim($_POST['brand_logo']),     //品牌logo名称
                "is_wanted" => 0,
                "status" => 0,
                "real_get" => (trim($_POST['real_get'])==trim($_POST['money'])*1)?trim($_POST['real_get']):trim($_POST['money'])*1,
                "description"=>trim($_POST['description']),
                "create_time" => date("Y-m-d H:i:s",$_SERVER['REQUEST_TIME'])
            );
            $key_id= $key->publishKey($data);
            if($key_id){
                //发布成功后
                $message=array(
                    "user_openid"=>$user_openid,
                    "url" =>U('/addon/Chengjiaojia/Key/getKeyDetail/id/'.$key_id),
                    "first"=>"您发布了一条线索",
                    "keyword1" => "发布线索提示",
                    "keyword2" => date("Y-m-d H:i:s",time())
                );
                sendNoticeMessage($message);

                //判断该线索是否为当天第一条线索
                $condition['user_openid'] = $user_openid;
                $condition['create_time'] = array("egt",date("Y-m-d",time()));
                $rst = M("cj_key")->where($condition)->select();
                if(count($rst) == 1){
                    //每日发布的第一条线索会有积分和经验奖励
                    $creditCon['user_openid'] = $user_openid;
                    $creditData['use_credit'] = 20;
                    $creditData['frozen_credit'] = 0;
                    changeCredit($creditCon,$creditData);

                    //记录积分明细
                    $description = "每日发布线索奖励积分";
                    $credit_data = array(
                        "user_openid" => $user_openid,
                        "credit" => "+20",
                        "title" => $description,
                        "description" => $description,
                        "create_time" => date("Y-m-d H:i:s",time())
                    );
                    addCreditLog($credit_data);

                    //增加经验
                    $jifen = 5;
                    $jifenCon['user_openid'] = $user_openid;
                    changeJifen($jifenCon,$jifen);
                }

                //判断该线索是否为当月第二十条线索
//                $condition['create_time'] = array("egt",date("Y-m",time()));
//                $rst = M("cj_key")->where($condition)->select();
//                if(count($rst) == 20){
//                    //每月发布的第二十条线索会有积分和经验奖励
//                    $creditCon['user_openid'] = $user_openid;
//                    $creditData['use_credit'] = 10;
//                    $creditData['frozen_credit'] = 0;
//                    changeCredit($creditCon,$creditData);
//
//                    //记录积分明细
//                    $description = "每日发布线索奖励积分";
//                    $credit_data = array(
//                        "user_openid" => $user_openid,
//                        "credit" => "+10",
//                        "title" => $description,
//                        "description" => $description,
//                        "create_time" => date("Y-m-d H:i:s",time())
//                    );
//                    addCreditLog($credit_data);
//
//                    //增加经验
//                    $jifen = 20;
//                    $jifenCon['user_openid'] = $user_openid;
//                    changeJifen($jifenCon,$jifen);
//                }

                //用户发布线索时，查找该线索的所属品牌的所有用户，并发送模板消息

                $brand_id = $_POST['brand_id'];
                $brand = $_POST['brand'];
                $userCon['brand_id'] = $brand_id;
                $userCon['status'] = 4;
                $userCon['city'] = trim($_POST['city']);
                $userList = M("cj_member")->where($userCon)->select();
                if(count($userList) > 0){
                    foreach($userList as $value){
                        $data['user_openid'] = $value['user_openid'];
                        $data['brand'] = $brand;
                        $data['key_id'] = $key_id;
                        sendBrandNotice($data);
                    }
                }
                //增加用户线索发布条数
                M("cj_member")->where("user_openid = '".$user_openid."'")->setInc("send_keys");
                echo 1;     //发布成功
            }else{
                echo -2;    //发布失败
            }
    	}

    	//真实线索
    	public function true(){
            $this->assign("key_id",$_GET['id']);
			$this->show(CUSTOM_TEMPLATE_PATH."/Key/key_true.html");
    	}

    	//虚假线索
    	public function false(){
            $this->assign("key_id",$_GET['id']);
    		$this->show(CUSTOM_TEMPLATE_PATH."/Key/key_false.html");
    	}

        /*
         * 评价线索
         * 注释
         * */
//        public function judge_bak(){
//            //线索编号
//            $id = $_GET['id'];
//            $user_openid = $this->user_openid;
//            //判断结果
//            $result = $_POST['result'];
//            //判断线索状态
//            $status = $this->checkStatus($id);
//            $key = new KeyModel();
//            //获取线索详情
//            $keyDetail = $this->getKey($id);
//
//            //该线索积分
//            $credit =   $keyDetail['credit'];
//
//            $bought = array();
//            $bought['user_openid'] = $user_openid;
//            $bought['key_id'] = $id;
//            //b1 购买 b1 判断
//            if($status == 11){
//                //b1 判断为有效
//                if($result == "true"){
//                    $bought_key_status = 1;
//                    $data['status'] =100;
//                    $data['judge_status'] = 1;
//                    $data['b1_agree_time'] = date("Y-m-d H:i:s",time());
//
//                    //线索被判断为有效时，对线索发布者进行积分奖励
//                    $data['use_credit'] = 5;
//                    $data['frozen_credit'] = 0;
//                    $condition['user_openid'] = $keyDetail['user_openid'];
//                    changeCredit($condition,$data);
//
//                    //将积分分给线索发布者
//                    $data['use_credit'] = $credit;
//                    $data['frozen_credit'] = 0;
//                    $condition['user_openid'] = $keyDetail['user_openid'];
//                    changeCredit($condition,$data);
//
//                    //线索发布者积分增加
//                    $description = "您有一条线索被确认有效，增加积分";
//                    $credit_data = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "credit" => "+10",
//                        "title" => $description,
//                        "description" => $description,
//                        "create_time" => date("Y-m-d H:i:s",time())
//                    );
//                    addCreditLog($credit_data);
//
//                    //如果线索判断为有效，同时将购买者的积分解冻
//                    $data['use_credit'] = 0;
//                    $data['frozen_credit'] = -$credit;
//                    $condition['user_openid'] = $user_openid;
//                    changeCredit($condition,$data);
//
//                    //对购买者积分解冻扣除
//                    $description = "您将一条线索被确认有效，扣除积分";
//                    $credit_data = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "credit" => "-10",
//                        "title" => $description,
//                        "description" => $description,
//                        "create_time" => date("Y-m-d H:i:s",time())
//                    );
//                    addCreditLog($credit_data);
//
//                    //对线索发布者 进行经验奖励
//                    $condition['user_openid'] = $keyDetail['user_openid'];
//                    $jifen = 10;
//                    changeJifen($condition,$jifen);
//
//                    //统计当月线索被判断为有效的次数
//                    $count = M("cj_key")->where("user_openid = '".$keyDetail['user_openid']."' and status >=100 and status <999 and create_time >= ".date("Y-m",time()))->count("id");
//                    if($count >= 10){
//                        //对线索发布者进行经验奖励
//                        $condition['user_openid'] = $keyDetail['user_openid'];
//                        $jifen = 30;
//                        changeJifen($condition,$jifen);
//
//                        //对线索发布者进行积分奖励
//                        $data['use_credit'] = 20;
//                        $data['frozen_credit'] = 0;
//                        $condition['user_openid'] = $keyDetail['user_openid'];
//                        changeCredit($condition,$data);
//                    }
//                    $message = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "url"   => U('/addon/Chengjiaojia/Key/publishKeyDetail/id/'.$id),
//                        "first" => "您有一条线索被采纳",
//                        "keyword1"=>"线索被采纳",
//                        "keyword2" => date("Y-m-d H:i:s",time())
//                    );
//                    sendNoticeMessage($message);
//
//                    //线索发布者有效线索数加1
//                    M("cj_member")->where("user_openid = '".$keyDetail['user_openid']."'")->setInc("useful_keys");
//
//                }
//                //b1 判断为无效
//                else{
//                    $bought_key_status = 2;
//                    $data['status'] = 1;
//                    $data['judge_status'] = 0;
//                    $data['b1_disagree_time'] = date("Y-m-d H:i:s",time());
//
//                    //线索评价为无效时，将购买者冻结积分解冻
//                    $credit =   $keyDetail['credit'];
//                    $data['use_credit'] = $credit;
//                    $data['frozen_credit'] = -$credit;
//                    $condition['user_openid'] = $user_openid;
//                    changeCredit($condition,$data);
//
//                    //对购买者积分解冻扣除
//                    $description = "您将一条线索被确认无效，解冻积分";
//                    $credit_data = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "credit" => "+10",
//                        "title" => $description,
//                        "description" => $description,
//                        "create_time" => date("Y-m-d H:i:s",time())
//                    );
//                    addCreditLog($credit_data);
//
//                    $message = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "url"   => U('/addon/Chengjiaojia/Key/publishKeyDetail/id/'.$id),
//                        "first" => "您有一条线索未被采纳",
//                        "keyword1"=>"线索未采纳",
//                        "keyword2" => date("Y-m-d H:i:s",time())
//                    );
//                    sendNoticeMessage($message);
//
//
//                }
//                //更新线索状态
//                if($key ->changeStatus($id,$data)){
//                    //添加用户判断记录
//                    $data['key_id'] = $id;
//                    $data['judge_openid'] = $user_openid;
//                    $data['judge_level'] = $_POST['judge_level'];
//                    $data['judge_reason'] = $_POST['judge_reason'];
//                    $data['judge_time'] = date("Y-m-d H:i:s",time());
//                    $key->addJudgeLog($data);
//
//                    //更新购买线索的状态
//                    $key->updateBoughtKey($bought,$bought_key_status);
//                    echo 1;
//                }
//                else{
//                    echo -1;
//                }
//            }
//            else if($status == 12){
//                //b2判断
//                if($result == "true"){
//                    $bought_key_status = 1;
//                    $data['status'] =200;
//                    $data['b2_agree_time'] = date("Y-m-d H:i:s",time());
//
//                    //如果线索判断为有效，同时将购买者的积分解冻
//                    $credit =   $keyDetail['credit'];
//                    $data['use_credit'] = 0;
//                    $data['frozen_credit'] = -$credit;
//                    $condition['user_openid'] = $user_openid;
//                    changeCredit($condition,$data);
//                    //将积分分给线索发布者
//                    $data['use_credit'] = $credit;
//                    $data['frozen_credit'] = 0;
//                    $condition['user_openid'] = $keyDetail['user_openid'];
//                    changeCredit($condition,$data);
//
//                    //线索被判断为有效时，对线索发布者进行积分奖励
//                    $data['use_credit'] = 5;
//                    $data['frozen_credit'] = 0;
//                    $condition['user_openid'] = $keyDetail['user_openid'];
//                    changeCredit($condition,$data);
//
//                    //对线索发布者 进行经验奖励
//                    $condition['user_openid'] = $keyDetail['user_openid'];
//                    $jifen = 10;
//                    changeJifen($condition,$jifen);
//
//                    //线索发布者积分增加
//                    $description = "您有一条线索被确认有效，增加积分";
//                    $credit_data = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "credit" => "+10",
//                        "title" => $description,
//                        "description" => $description,
//                        "create_time" => date("Y-m-d H:i:s",time())
//                    );
//                    addCreditLog($credit_data);
//
//                    //对购买者积分解冻扣除
//                    $description = "您将一条线索被确认有效，扣除积分";
//                    $credit_data = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "credit" => "-10",
//                        "title" => $description,
//                        "description" => $description,
//                        "create_time" => date("Y-m-d H:i:s",time())
//                    );
//                    addCreditLog($credit_data);
//                    //统计当月线索被判断为有效的次数
//                    $count = M("cj_key")->where("user_openid = '".$keyDetail['user_openid']."' and status >=100 and status <999 and create_time >= ".date("Y-m",time()))->count("id");
//                    if($count >= 10){
//                        //对线索发布者进行经验奖励
//                        $condition['user_openid'] = $keyDetail['user_openid'];
//                        $jifen = 30;
//                        changeJifen($condition,$jifen);
//
//                        //对线索发布者进行积分奖励
//                        $data['use_credit'] = 20;
//                        $data['frozen_credit'] = 0;
//                        $condition['user_openid'] = $keyDetail['user_openid'];
//                        changeCredit($condition,$data);
//                    }
//
//                    M("cj_member")->where("user_openid = '".$keyDetail['user_openid']."'")->setInc("useful_keys");
//                    $message = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "url"   => U('/addon/Chengjiaojia/Key/publishKeyDetail/id/'.$id),
//                        "first" => "您有一条线索被采纳",
//                        "keyword1"=>"线索被采纳",
//                        "keyword2" => date("Y-m-d H:i:s",time())
//                );
//                    sendNoticeMessage($message);
//                }
//                else{
//                    $bought_key_status = 2;
//                    $data['status'] = 2;
//                    $data['judge_status'] = 0;
//                    $data['b2_disagree_time'] = date("Y-m-d H:i:s",time());
//
//                    //线索评价为无效时，将购买者冻结积分解冻
//                    $credit =   $keyDetail['credit'];
//                    $data['use_credit'] = $credit;
//                    $data['frozen_credit'] = -$credit;
//                    $condition['user_openid'] = $user_openid;
//                    changeCredit($condition,$data);
//                    //对购买者积分解冻扣除
//                    $description = "您将一条线索被确认无效，解冻积分";
//                    $credit_data = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "credit" => "+10",
//                        "title" => $description,
//                        "description" => $description,
//                        "create_time" => date("Y-m-d H:i:s",time())
//                    );
//                    addCreditLog($credit_data);
//                    $message = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "url"   => U('/addon/Chengjiaojia/Key/publishKeyDetail/id/'.$id),
//                        "first" => "您有一条线索未被采纳",
//                        "keyword1"=>"线索未采纳",
//                        "keyword2" => date("Y-m-d H:i:s",time())
//                    );
//                    sendNoticeMessage($message);
//                }
//
//                //更新线索状态
//                if($key ->changeStatus($id,$data)){
//                    //添加用户判断记录
//                    $data['key_id'] = $id;
//                    $data['judge_openid'] = $user_openid;
//                    $data['judge_level'] = $_POST['judge_level'];
//                    $data['judge_reason'] = $_POST['judge_reason'];
//                    $data['judge_time'] = date("Y-m-d H:i:s",time());
//                    $key->addJudgeLog($data);
//
//                    $key->updateBoughtKey($bought,$bought_key_status);
//
//                    echo 1;
//                }
//                else{
//                    echo -1;
//                }
//            }
//            else if($status == 13){
//                //b3判断
//                if($result == "true"){
//                    $bought_key_status = 1;
//                    $data['status'] =300;
//                    $data['b2_agree_time'] = date("Y-m-d H:i:s",time());
//                    $data['judge_status'] = 1;
//
//                    //如果线索判断为有效，同时将购买者的积分解冻
//                    $credit =   $keyDetail['credit'];
//                    $data['use_credit'] = 0;
//                    $data['frozen_credit'] = -$credit;
//                    $condition['user_openid'] = $user_openid;
//                    changeCredit($condition,$data);
//
//                    //将积分分给线索发布者
//                    $data['use_credit'] = $credit;
//                    $data['frozen_credit'] = 0;
//                    $condition['user_openid'] = $keyDetail['user_openid'];
//                    changeCredit($condition,$data);
//
//                    //线索被判断为有效时，对线索发布者进行积分奖励
//                    $data['use_credit'] = 5;
//                    $data['frozen_credit'] = 0;
//                    $condition['user_openid'] = $keyDetail['user_openid'];
//                    changeCredit($condition,$data);
//
//                    //对线索发布者 进行经验奖励
//                    $condition['user_openid'] = $keyDetail['user_openid'];
//                    $jifen = 10;
//                    changeJifen($condition,$jifen);
//
//                    //线索发布者积分增加
//                    $description = "您有一条线索被确认有效，增加积分";
//                    $credit_data = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "credit" => "+10",
//                        "title" => $description,
//                        "description" => $description,
//                        "create_time" => date("Y-m-d H:i:s",time())
//                    );
//                    addCreditLog($credit_data);
//
//                    //对购买者积分解冻扣除
//                    $description = "您将一条线索被确认有效，扣除积分";
//                    $credit_data = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "credit" => "-10",
//                        "title" => $description,
//                        "description" => $description,
//                        "create_time" => date("Y-m-d H:i:s",time())
//                    );
//                    addCreditLog($credit_data);
//                    //统计当月线索被判断为有效的次数
//                    $count = M("cj_key")->where("user_openid = '".$keyDetail['user_openid']."' and status >=100 and status <999 and create_time >= ".date("Y-m",time()))->count("id");
//                    if($count >= 10){
//                        //对线索发布者进行经验奖励
//                        $condition['user_openid'] = $keyDetail['user_openid'];
//                        $jifen = 30;
//                        changeJifen($condition,$jifen);
//
//                        //对线索发布者进行积分奖励
//                        $data['use_credit'] = 20;
//                        $data['frozen_credit'] = 0;
//                        $condition['user_openid'] = $keyDetail['user_openid'];
//                        changeCredit($condition,$data);
//                    }
//
//                    //线索发布者被评为有效的线索条数+1
//                    M("cj_member")->where("user_openid = '".$keyDetail['user_openid']."'")->setInc("useful_keys");
//
//                    //给线索发布者发送模板消息
//                    $message = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "url"   => U('/addon/Chengjiaojia/Key/publishKeyDetail/id/'.$id),
//                        "first" => "您有一条线索被采纳",
//                        "keyword1"=>"线索被采纳",
//                        "keyword2" => date("Y-m-d H:i:s",time())
//                    );
//                    sendNoticeMessage($message);
//                }
//                else{
//                    $bought_key_status = 2;
//                    $data['status'] = 999;
//                    $data['judge_status'] = 0;
//                    $data['b3_disagree_time'] = date("Y-m-d H:i:s",time());
//
//                    //线索评价为无效时，将购买者冻结积分解冻
//                    $credit =   $keyDetail['credit'];
//                    $data['use_credit'] = $credit;
//                    $data['frozen_credit'] = -$credit;
//                    $condition['user_openid'] = $user_openid;
//                    changeCredit($condition,$data);
//
//                    //对购买者积分解冻扣除
//                    $description = "您将一条线索被确认无效，解冻积分";
//                    $credit_data = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "credit" => "+10",
//                        "title" => $description,
//                        "description" => $description,
//                        "create_time" => date("Y-m-d H:i:s",time())
//                    );
//                    addCreditLog($credit_data);
//                    //给线索发布者推送模板消息
//                    $message = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "url"   => U('/addon/Chengjiaojia/Key/publishKeyDetail/id/'.$id),
//                        "first" => "您有一条线索未被采纳",
//                        "keyword1"=>"线索未采纳",
//                        "keyword2" => date("Y-m-d H:i:s",time())
//                    );
//                    sendNoticeMessage($message);
//                }
//
//                //更新线索状态
//                if($key ->changeStatus($id,$data)){
//                    //添加用户判断记录
//                    $data['key_id'] = $id;
//                    $data['judge_openid'] = $user_openid;
//                    $data['judge_level'] = $_POST['judge_level'];
//                    $data['judge_reason'] = $_POST['judge_reason'];
//                    $data['judge_time'] = date("Y-m-d H:i:s",time());
//                    $key->addJudgeLog($data);
//
//                    $key->updateBoughtKey($bought,$bought_key_status);
//
//                    echo 1;
//                }
//                else{
//                    echo -1;
//                }
//            }
//            else{
//                echo -2;
//            }
//        }

        //评价线索
        public function judge(){
            //线索编号
            $id = $_POST['key_id'];
            $user_openid = $this->user_openid;
            //判断结果
            $result = $_POST['result'];
            //判断线索状态
            $status = $this->checkStatus($id);
            $key = new KeyModel();
            //获取线索详情
            $keyDetail = $this->getKey($id);

            //该线索价值
            $account_pay =   $keyDetail['money'];       //购买者应支付的金额
            $account_get = $keyDetail['real_get'];      //线索发布者获得的金额（扣除平台抽佣后的实际价格）
            $bought = array();
            $bought['user_openid'] = $user_openid;
            $bought['key_id'] = $id;
            //b1 购买 b1 判断
            if($status == 11){
                //b1 判断为有效
                if($result == "true"){
                    $bought_key_status = 1;
                    $data['status'] =100;
                    $data['judge_status'] = 1;
                    $data['b1_agree_time'] = date("Y-m-d H:i:s",time());

                    /*
                     * 线索被判断为有效时，对线索发布者进行积分奖励
                     * 积分
                     * */
                    $data['use_credit'] = 200;
                    $data['frozen_credit'] = 0;
                    $condition['user_openid'] = $keyDetail['user_openid'];
                    changeCredit($condition,$data);
                    //积分变动记录
                    $description = "您有一条线索被确认为有效，增加积分";
                    $credit_data = array(
                        "user_openid" => $keyDetail['user_openid'],
                        "credit" => "+200",
                        "title" => $description,
                        "description" => $description,
                        "create_time" => date("Y-m-d H:i:s",time())
                    );
                    addCreditLog($credit_data);
                    /*
                     * 将橙蕉豆分给线索发布者
                     * 橙蕉豆
                     * 线索发布者
                     * */
                    $data['use_account'] = $account_get;
                    $data['frozen_account'] = 0;
                    $condition['user_openid'] = $keyDetail['user_openid'];
                    changeAccount($condition,$data);

                    /*
                     * 线索发布者橙蕉豆增加 添加橙蕉记录
                     * 线索发布者
                     * 橙蕉豆
                     * */
                    $description = "您有一条线索被确认有效，增加橙蕉豆";
                    $account_data = array(
                        "user_openid" => $keyDetail['user_openid'],
                        "account" => $account_get,
                        "remark" => "+".$account_get,
                        "desc" => $description,
                        "type" => 4,
                        "status" => 1,
                        "create_time" => date("Y-m-d H:i:s",time())
                    );
                    addAccountLog($account_data);

                    /*
                     * 如果线索判断为有效，同时将购买者的橙蕉豆解冻
                     * 橙蕉豆
                     * 线索购买者
                     * */
                    $data['use_account'] = 0;
                    $data['frozen_account'] = -$account_pay;
                    $condition['user_openid'] = $user_openid;
                    changeAccount($condition,$data);

                    //对购买者橙蕉豆解冻扣除 添加橙蕉记录
                    $description = "您将一条线索确认为有效，扣除橙蕉豆";
                    $account_data = array(
                        "user_openid" => $this->user_openid,
                        "account" => $account_pay,
                        "remark" => "-".$account_pay,
                        "desc" => $description,
                        "type" => 2,
                        "status" => 1,
                        "create_time" => date("Y-m-d H:i:s",time())
                    );
                    addAccountLog($account_data);

                    //对线索发布者 进行经验奖励
                    $condition['user_openid'] = $keyDetail['user_openid'];
                    $jifen = 10;
                    changeJifen($condition,$jifen);

                    //统计当月线索被判断为有效的次数
                    //取消
//                    $count = M("cj_key")->where("user_openid = '".$keyDetail['user_openid']."' and status >=100 and status <999 and create_time >= ".date("Y-m",time()))->count("id");
//                    if($count >= 10){
//                        //对线索发布者进行经验奖励
//                        $condition['user_openid'] = $keyDetail['user_openid'];
//                        $jifen = 30;
//                        changeJifen($condition,$jifen);
//
//                        //对线索发布者进行积分奖励
//                        $data['use_credit'] = 20;
//                        $data['frozen_credit'] = 0;
//                        $condition['user_openid'] = $keyDetail['user_openid'];
//                        changeCredit($condition,$data);
//                    }

                    /*
                     * 发送模板消息
                     * */
                    $message = array(
                        "user_openid" => $keyDetail['user_openid'],
                        "url"   => U('/addon/Chengjiaojia/Key/publishKeyDetail/id/'.$id),
                        "first" => "您有一条线索被采纳",
                        "keyword1"=>"线索被采纳",
                        "keyword2" => date("Y-m-d H:i:s",time())
                    );
                    sendNoticeMessage($message);

                    //线索发布者有效线索数加1
                    M("cj_member")->where("user_openid = '".$keyDetail['user_openid']."'")->setInc("useful_keys");

                }
                /*
                 * b1 判断为无效
                 * 此时线索处于冻结状态，需要线索发布者对该判断进行验证
                 * */
                else{
                    $bought_key_status = 2;
                    $data['status'] = -1;
                    $data['judge_status'] = 0;
                    $data['b1_disagree_time'] = date("Y-m-d H:i:s",time());

                    //发送评价模板消息
                    $message = array(
                        "user_openid" => $keyDetail['user_openid'],
                        "url"   => U('/addon/Chengjiaojia/Key/publishKeyDetail/id/'.$id),
                        "first" => "您有一条线索未被采纳",
                        "keyword1"=>"线索未被采纳",
                        "keyword2" => date("Y-m-d H:i:s",time())
                    );
                    sendNoticeMessage($message);
                }
                /*
                 * 更新线索状态
                 * 添加b1判断结果
                 * */
                if($key ->changeStatus($id,$data)){
                    //添加用户判断记录
                    $data['key_id'] = $id;
                    $data['judge_openid'] = $user_openid;
                    $data['judge_reason'] = $_POST['judge_reason'];
                    $data['judge_time'] = date("Y-m-d H:i:s",time());
                    $key->addJudgeLog($data);

                    //更新购买线索的状态
                    $key->updateBoughtKey($bought,$bought_key_status);
                    echo 1;
                }
                else{
                    echo -1;
                }
            }
            //b2 购买 b2判断
            else if($status == 12){
                if($result == "true"){
                    $bought_key_status = 1;
                    $data['status'] =200;
                    $data['judge_status'] = 1;
                    $data['b1_agree_time'] = date("Y-m-d H:i:s",time());

                    //线索被判断为有效时，对线索发布者进行积分奖励
                    $data['use_credit'] = 5;
                    $data['frozen_credit'] = 0;
                    $condition['user_openid'] = $keyDetail['user_openid'];
                    changeCredit($condition,$data);
                    //积分变动记录
                    $description = "您有一条线索被确认为有效，增加积分";
                    $credit_data = array(
                        "user_openid" => $keyDetail['user_openid'],
                        "credit" => "+200",
                        "title" => $description,
                        "description" => $description,
                        "create_time" => date("Y-m-d H:i:s",time())
                    );
                    //将橙蕉豆分给线索发布者
                    $data['use_account'] = $account_get;
                    $data['frozen_account'] = 0;
                    $condition['user_openid'] = $keyDetail['user_openid'];
                    changeAccount($condition,$data);

                    //线索发布者橙蕉豆增加 添加橙蕉记录
                    $description = "您有一条线索被确认有效，增加橙蕉豆";
                    $account_data = array(
                        "user_openid" => $keyDetail['user_openid'],
                        "account" => $account_get,
                        "remark" => "+".$account_get,
                        "desc" => $description,
                        "type" => 4,
                        "status" => 1,
                        "create_time" => date("Y-m-d H:i:s",time())
                    );
                    addAccountLog($account_data);

                    //如果线索判断为有效，同时将购买者的橙蕉豆解冻
                    $data['use_account'] = 0;
                    $data['frozen_account'] = -$account_pay;
                    $condition['user_openid'] = $user_openid;
                    changeAccount($condition,$data);

                    //对购买者橙蕉豆解冻扣除 添加橙蕉记录
                    $description = "您将一条线索被确认有效，扣除积分";
                    $account_data = array(
                        "user_openid" => $keyDetail['user_openid'],
                        "account" => $account_pay,
                        "remark" => "-".$account_pay,
                        "desc" => $description,
                        "type" => 2,
                        "status" => 1,
                        "create_time" => date("Y-m-d H:i:s",time())
                    );
                    addAccountLog($account_data);

                    //对线索发布者 进行经验奖励
                    $condition['user_openid'] = $keyDetail['user_openid'];
                    $jifen = 10;
                    changeJifen($condition,$jifen);

                    //统计当月线索被判断为有效的次数
                    $count = M("cj_key")->where("user_openid = '".$keyDetail['user_openid']."' and status >=100 and status <999 and create_time >= ".date("Y-m",time()))->count("id");
                    if($count >= 10){
                        //对线索发布者进行经验奖励
                        $condition['user_openid'] = $keyDetail['user_openid'];
                        $jifen = 30;
                        changeJifen($condition,$jifen);

                        //对线索发布者进行积分奖励
                        $data['use_credit'] = 20;
                        $data['frozen_credit'] = 0;
                        $condition['user_openid'] = $keyDetail['user_openid'];
                        changeCredit($condition,$data);
                    }
                    $message = array(
                        "user_openid" => $keyDetail['user_openid'],
                        "url"   => U('/addon/Chengjiaojia/Key/publishKeyDetail/id/'.$id),
                        "first" => "您有一条线索被采纳",
                        "keyword1"=>"线索被采纳",
                        "keyword2" => date("Y-m-d H:i:s",time())
                    );
                    sendNoticeMessage($message);

                    //线索发布者有效线索数加1
                    M("cj_member")->where("user_openid = '".$keyDetail['user_openid']."'")->setInc("useful_keys");

                }
                else{
                    $bought_key_status = -2;
                    $data['status'] = 2;
                    $data['judge_status'] = 0;
                    $data['b2_disagree_time'] = date("Y-m-d H:i:s",time());

//                    //线索评价为无效时，将购买者冻结积分解冻
//                    $credit =   $keyDetail['credit'];
//                    $data['use_credit'] = $credit;
//                    $data['frozen_credit'] = -$credit;
//                    $condition['user_openid'] = $user_openid;
//                    changeCredit($condition,$data);
//                    //对购买者积分解冻扣除
//                    $description = "您将一条线索被确认无效，解冻积分";
//                    $credit_data = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "credit" => "+10",
//                        "title" => $description,
//                        "description" => $description,
//                        "create_time" => date("Y-m-d H:i:s",time())
//                    );
//                    addCreditLog($credit_data);
                    $message = array(
                        "user_openid" => $keyDetail['user_openid'],
                        "url"   => U('/addon/Chengjiaojia/Key/publishKeyDetail/id/'.$id),
                        "first" => "您有一条线索未被采纳",
                        "keyword1"=>"线索未采纳",
                        "keyword2" => date("Y-m-d H:i:s",time())
                    );
                    sendNoticeMessage($message);
                }

                //更新线索状态
                if($key ->changeStatus($id,$data)){
                    //添加用户判断记录
                    $data['key_id'] = $id;
                    $data['judge_openid'] = $user_openid;
                    $data['judge_level'] = $_POST['judge_level'];
                    $data['judge_reason'] = $_POST['judge_reason'];
                    $data['judge_time'] = date("Y-m-d H:i:s",time());
                    $key->addJudgeLog($data);

                    $key->updateBoughtKey($bought,$bought_key_status);

                    echo 1;
                }
                else{
                    echo -1;
                }
            }
            //b3 购买 b3判断

//            else if($status == 13){
//                //b3判断
//                if($result == "true"){
//                    $bought_key_status = 1;
//                    $data['status'] =300;
//                    $data['judge_status'] = 1;
//                    $data['b1_agree_time'] = date("Y-m-d H:i:s",time());
//
//                    //线索被判断为有效时，对线索发布者进行积分奖励
//                    $data['use_credit'] = 5;
//                    $data['frozen_credit'] = 0;
//                    $condition['user_openid'] = $keyDetail['user_openid'];
//                    changeCredit($condition,$data);
//
//                    //将橙蕉豆分给线索发布者
//                    $data['use_account'] = $account;
//                    $data['frozen_account'] = 0;
//                    $condition['user_openid'] = $keyDetail['user_openid'];
//                    changeAccount($condition,$data);
//
//                    //线索发布者橙蕉豆增加 添加橙蕉记录
//                    $description = "您有一条线索被确认有效，增加橙蕉豆";
//                    $account_data = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "account" => $account,
//                        "remark" => "+".$account,
//                        "desc" => $description,
//                        "type" => 4,
//                        "status" => 1,
//                        "create_time" => date("Y-m-d H:i:s",time())
//                    );
//                    addAccountLog($account_data);
//
//                    //如果线索判断为有效，同时将购买者的橙蕉豆解冻
//                    $data['use_account'] = 0;
//                    $data['frozen_account'] = -$account;
//                    $condition['user_openid'] = $user_openid;
//                    changeAccount($condition,$data);
//
//                    //对购买者橙蕉豆解冻扣除 添加橙蕉记录
//                    $description = "您将一条线索被确认有效，扣除积分";
//                    $account_data = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "account" => $account,
//                        "remark" => "-".$account,
//                        "desc" => $description,
//                        "type" => 2,
//                        "status" => 1,
//                        "create_time" => date("Y-m-d H:i:s",time())
//                    );
//                    addAccountLog($account_data);
//
//                    //对线索发布者 进行经验奖励
//                    $condition['user_openid'] = $keyDetail['user_openid'];
//                    $jifen = 10;
//                    changeJifen($condition,$jifen);
//
//                    //统计当月线索被判断为有效的次数
//                    $count = M("cj_key")->where("user_openid = '".$keyDetail['user_openid']."' and status >=100 and status <999 and create_time >= ".date("Y-m",time()))->count("id");
//                    if($count >= 10){
//                        //对线索发布者进行经验奖励
//                        $condition['user_openid'] = $keyDetail['user_openid'];
//                        $jifen = 30;
//                        changeJifen($condition,$jifen);
//
//                        //对线索发布者进行积分奖励
//                        $data['use_credit'] = 20;
//                        $data['frozen_credit'] = 0;
//                        $condition['user_openid'] = $keyDetail['user_openid'];
//                        changeCredit($condition,$data);
//                    }
//                    $message = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "url"   => U('/addon/Chengjiaojia/Key/publishKeyDetail/id/'.$id),
//                        "first" => "您有一条线索被采纳",
//                        "keyword1"=>"线索被采纳",
//                        "keyword2" => date("Y-m-d H:i:s",time())
//                    );
//                    sendNoticeMessage($message);
//
//                    //线索发布者有效线索数加1
//                    M("cj_member")->where("user_openid = '".$keyDetail['user_openid']."'")->setInc("useful_keys");
//
//                }
//                else{
//                    $bought_key_status = 2;
//                    $data['status'] = 999;
//                    $data['judge_status'] = 0;
//                    $data['b3_disagree_time'] = date("Y-m-d H:i:s",time());
//
////                    //线索评价为无效时，将购买者冻结积分解冻
////                    $credit =   $keyDetail['credit'];
////                    $data['use_credit'] = $credit;
////                    $data['frozen_credit'] = -$credit;
////                    $condition['user_openid'] = $user_openid;
////                    changeCredit($condition,$data);
////
////                    //对购买者积分解冻扣除
////                    $description = "您将一条线索被确认无效，解冻积分";
////                    $credit_data = array(
////                        "user_openid" => $keyDetail['user_openid'],
////                        "credit" => "+10",
////                        "title" => $description,
////                        "description" => $description,
////                        "create_time" => date("Y-m-d H:i:s",time())
////                    );
////                    addCreditLog($credit_data);
//                    //给线索发布者推送模板消息
//                    $message = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "url"   => U('/addon/Chengjiaojia/Key/publishKeyDetail/id/'.$id),
//                        "first" => "您有一条线索未被采纳",
//                        "keyword1"=>"线索未采纳",
//                        "keyword2" => date("Y-m-d H:i:s",time())
//                    );
//                    sendNoticeMessage($message);
//                }
//
//                //更新线索状态
//                if($key ->changeStatus($id,$data)){
//                    //添加用户判断记录
//                    $data['key_id'] = $id;
//                    $data['judge_openid'] = $user_openid;
//                    $data['judge_level'] = $_POST['judge_level'];
//                    $data['judge_reason'] = $_POST['judge_reason'];
//                    $data['judge_time'] = date("Y-m-d H:i:s",time());
//                    $key->addJudgeLog($data);
//
//                    $key->updateBoughtKey($bought,$bought_key_status);
//
//                    echo 1;
//                }
//                else{
//                    echo -1;
//                }
//            }

            //线索状态出错

            else{
                echo -2;
            }
        }

        //统计发布线索条数
        private function getKeyNum(){
            $data['user_openid'] = session("user_openid");
            $data['status'] = 1;

            $m = new KeyModel();
            $result = $m -> getUsefulKeys($data);
            return $result;
        }

        /*
         * 发布线索列表页
         * 用户查看个人发布线索列表（已购买/未购买）
         * */
        public function publishList(){
            $user_openid = $this->user_openid;
            $result = array();
            //未购买线索
            $user["user_openid"] = array("eq",$user_openid);
            $status['status'] = array("eq",0);
            $key = new KeyModel();
            $unBought = $key->getKeyList($status,$user);
            $this->assign("unBought",$unBought);
            //已够买线索
            $status['status'] = array("neq",0);
            $Bought = $key->getKeyList($status,$user);

            $this->assign("Bought",$Bought);

            $this->show(CUSTOM_TEMPLATE_PATH."/Key/publishList.html");
        }

        /*
         * 购买的线索列表页
         *
         * 用户查看个人已购买的线索列表页
         *
         * 待验证/已验证
         * 未及时验证的，一定期限后自动验证
         * */

        public function boughtKeyList(){
            $user_openid = $this->user_openid;
            $key = new KeyModel();
            $result = array();
            //已验证
            $status['wp_cj_key_bought.status'] = array("neq",0);
            $result['verified'] = $key->getBoughtKey($status,$user_openid);

            $this->assign("verified",$result['verified']);
            //未验证
            $status['wp_cj_key_bought.status'] = array("eq",0);
            $result['unverified'] = $key->getBoughtKey($status,$user_openid);
            $this->assign("unverified",$result['unverified']);
            $this->show(CUSTOM_TEMPLATE_PATH."/Key/boughtKeyList.html");
        }

        /*
         * 通话 验证线索准确性
         * */

        public function call(){
            $id = I("id");
            $status = $this->checkStatus($id);
            $key = new KeyModel();
            //获取b1相关购买信息
            $info = $key -> getKeyDetail($id);
            //客户电话
            $custom_telephone = $info['custom_telephone'];
            //判断当前购买用户
            $buyer_telephone = '';
            //b1购买
            if($status == 1){
                //购买者电话
                $buyer_telephone = $info['b1_telephone'];
            }else if($status == 2){
                //购买者电话
                $buyer_telephone = $info['b2_telephone'];
            }
            //调用通话接口
            $custom_telephone = "18652362600";
            $buyer_telephone = "13661673982";

            $time = date("Y-m-d H:i:s",time());

            $call = new Util\Record();
            $result = $call -> Call($custom_telephone,$buyer_telephone);
            echo json_encode($result);
        }

        public function GetFreeDayDetail(){
            $call = new Util\Record();
            $time = date("Y-m-d",time());
            $result = $call -> GetFreeDayDetail($time);
            echo json_encode($result);
        }


        /*
         * 线索发布列表
         * 从线索发布者的角度
         * */
        /*
         *获取线索详情
         * 从线索发布者的角度
         * */
        public function publishKeyDetail(){
            $id = $_GET['id'];
            $this->assign("id",$id);
            $key = new KeyModel();
            //已发布 未验证
            $result = $key -> getKeyDetail($id);
            $this->assign("status",$result['status']);
            //获取该线索的评价信息
            $judgeInfo = M("cj_judge")->join("wp_cj_member on wp_cj_member.user_openid = wp_cj_judge.judge_openid")->where("wp_cj_judge.key_id = ".$id)->order("wp_cj_judge.judge_time desc")->field("wp_cj_judge.id,wp_cj_judge.judge_reason,wp_cj_judge.judge_level,wp_cj_judge.judge_time,wp_cj_member.name")->select();

            //获取线索购买者相关信息
            $data['wp_cj_key.id'] = $id;
            $model = M("cj_key");
            if($result['status'] == 11 ||$result['status'] == 100 ||$result['status'] == -1 ||$result['status'] == 1){
                $keyPublisherInfo = $model->join("wp_cj_member ON wp_cj_member.user_openid = wp_cj_key.b1_openid")->where($data)->field("wp_cj_member.avatar,wp_cj_member.name,wp_cj_member.telephone,wp_cj_member.bought_keys,wp_cj_member.useful_keys")->find();
            }else if($result['status'] == 12 || $result['status'] == 200 || $result['status'] == -2 ||$result['status'] == 2){
                $keyPublisherInfo = $model->join("wp_cj_member ON wp_cj_member.user_openid = wp_cj_key.b2_openid")->where($data)->field("wp_cj_member.avatar,wp_cj_member.name,wp_cj_member.telephone,wp_cj_member.bought_keys,wp_cj_member.useful_keys")->find();
            }else if($result['status'] == 13){
                $keyPublisherInfo = $model->join("wp_cj_member ON wp_cj_member.user_openid = wp_cj_key.b3_openid")->where($data)->field("wp_cj_member.avatar,wp_cj_member.name,wp_cj_member.telephone,wp_cj_member.bought_keys,wp_cj_member.useful_keys")->find();
            }
            $this->assign("judgeInfo",$judgeInfo);
            $this->assign("userInfo",$keyPublisherInfo);
            $this->assign("result",$result);
            $this->show(CUSTOM_TEMPLATE_PATH."/Key/userKeyDetail.html");
        }


        /*
         * 线索库
         * 揭榜时，展示用户可使用的线索
         * */
        public function availableKey(){
//            $user_openid = $this->user_openid;
            $user_openid = "o0nM4t4nSz5FDTJERyVctDjrylKw";
            //悬赏编号
            $wanted_id = $_GET['id'];
            $key = new KeyModel();
            $result = $key->getAvailableKeys($user_openid);
//            var_dump($result);exit;
            $this->assign("wanted_id",$wanted_id);
            $this->assign("result",$result);
            $this->show(CUSTOM_TEMPLATE_PATH."/Key/keyAvailable.html");
        }

        /*
         * 线索修改
         * */

        public function edit(){
            $id = $_GET['id'];
            //获取线索详情
            $key = new KeyModel();
            $result = $key->getKeyDetail($id);
            $this->assign("id",$id);
            $this->assign("result",$result);
            $this->show(CUSTOM_TEMPLATE_PATH."/Key/edit.html");
        }

        /*
         *
         * 更新线索
         * */
        public function update(){
            $id = $_POST['id'];
            unset($_POST['id']);
            $data = $_POST;
            $key = new KeyModel();
            if($key->update($id,$data)){
                echo 1;
            }else{
                echo -1;
            }
        }

        /*
         * 创建揭榜线索
         * */
        public function publishJieBangKey(){
            $wanted_id = $_GET['id'];

            $this->assign("wanted_id",$wanted_id);
            $this->show(CUSTOM_TEMPLATE_PATH."/Key/publishJiebangKey.html");
        }

        /*
         * 新建线索并揭榜
         * */

        public function jieBang(){
            //新建线索
            $m = new MemberModel();
            //获取用户信息
            $user_openid = $this->user_openid;

            //判断用户积分情况
            $res = $m->getMemberInfo($user_openid);

            $key = new KeyModel();
            //获取线索配置信息
            $data = array(
                "user_openid" => $user_openid,              //用户编号
                "province"  => trim($_POST['province']),          //线索所属省
                "city"  => trim($_POST['city']),                  //线索所属市/区
                "area"=> trim($_POST['area']),
                "brand" => trim($_POST['brand']),                 //线索所属品牌
                "series" => trim($_POST['series']),               //线索所属系列
                "time_limit" => trim($_POST['time_limit']),       //线索时间限制
                "custom_name" => trim($_POST['custom_name']),     //客户姓名
                "custom_telephone" => trim($_POST['custom_telephone']),   //客户电话
                "key_rate" => round(trim($_POST['key_rate']),1),                 //等级要求
                "credit" => trim($_POST['credit']),               //线索积分
                "is_wanted" => 0,
                "status" => 0,
                "description"=>trim($_POST['description']),
                "create_time" => date("Y-m-d H:i:s",$_SERVER['REQUEST_TIME'])
            );
            $result =$key->publishKey($data);
            if($result>0){
                //创建线索成功后，进行揭榜操作
                $key_id = $result;
                $wanted_id = $_GET['id'];
                $user_openid = $this->user_openid;
                $wanted = new WantedModel();

                //判断该悬赏是否已经结束
                $agreeStatus = $wanted->checkAgreeStatus($wanted_id);
                //判断悬赏发布者
                $wanted_openid = $wanted->checkMember($wanted_id);
                //查询悬赏发布者个人信息
                $member = new MemberModel();
                $wantedInfo = $member->getOne($wanted_openid);
                $num = 0;

                //线索处理
                $key = new KeyModel();

                if($agreeStatus == 1){
                    //如果悬赏已结束，则停止
                    echo -1;
                    exit;
                }else{
                    //如果悬赏状态正确，则添加用户揭榜记录
                    //判断线索状态

                    //查询该线索是否已经揭榜
                    $data['publisher_openid'] = $wanted_openid;     //线索发布者编号
                    $data['get_openid'] = $user_openid;              //揭榜者编号
                    $data['wanted_id'] = $wanted_id;                 //悬赏编号
                    $data['key_id'] = $key_id;                             //线索编号
                    $data['status'] = 0;                               //初始状态 0
                    $data['get_time'] = date("Y-m-d H:i:s",time()); //揭榜时间
                    if($wanted->saveWantedGet($data)){
                        echo 1;
                        $content['is_wanted'] = 1;
                        //更新该条线索状态
                        $key->update($key_id,$content);
                    }else{
                        echo -2;
                    }
                }
            }else{
                echo -2;    //发布失败
            }

        }

        //晒单时，查看已完成线索列表
        public function getFinishedKey(){
            $user_openid = $this->user_openid;
            $key = new KeyModel();
            $result = array();
            //已验证
            $status['wp_cj_key_bought.status'] = array("gt",0);
            $result['verified'] = $key->getBoughtKey($status,$user_openid);
            $this->assign("verified",$result['verified']['rows']);
            $this->show(CUSTOM_TEMPLATE_PATH."/Key/finishedKey.html");
        }


        /*
         * 用户购买线索时，添加购买信息
         * */

        public function addKeyBought($data){
            return M("cj_key_bought")->add($data);
        }

        //用户从消息中心查看已购买的线索
        public function getUserBoughtKey(){
            $id = $_GET['id'];
            $condition['user_openid'] = $this->user_openid;
            $condition['key_id'] = $id;
            $status = M("cj_key_bought")->where($condition)->getField("status");
            if($status == 0){
                $this->getBoughtKey($id);
            }else{
                $this->getVerifiedKeyDetail($id);
            }
        }

        //判断用户当前购买线索是否跟线索发布者为同一人
        public function checkKeyBuyer(){
            $id = $_POST['id'];
            $user_openid = $this->user_openid;
            $member = $this->checkMember($id);
            //获取购买者信息
            if($user_openid == $member) {
                echo -1;
                exit;
            }else{
                echo 1;
                exit;
            }
        }

        //用户购买线索后，24小时内如果不对线索进行评价 的话，系统自动评为有效
        public function getOverJudge(){
            $data['status'] = array("BETWEEN",array("11","99"));
            $result = M("cj_key")->where($data)->select();
            $keys = array();
            $time = date("Y-m-d H:i:s",time()-24*60*60);
            foreach($result as $key=>$value){
                if($value['status'] == 11){
                    if($value['b1_buy_time'] <$time){
                        $keys[$key]['id'] = $value['id'];
                        $keys[$key]['buy_openid'] = $value['b1_openid'];
                    }
                }else if($value['status'] == 12){
                    if($value['b2_buy_time'] <$time){
                        $keys[$key]['id'] = $value['id'];
                        $keys[$key]['buy_openid'] = $value['b2_openid'];
                    }
                }else if($value['status'] == 13){
                    if($value['b3_buy_time'] <$time){
                        $keys[$key]['id'] = $value['id'];
                        $keys[$key]['buy_openid'] = $value['b3_openid'];
                    }
                }
            }
            foreach($keys as $k=>$v){
                $this->autoJudge($v['id'],$v['buy_openid']);
            }

        }

//        public function autoJudge($key_id,$user_openid){
//            //线索编号
//            $id = $key_id;
//            //判断结果
//            $result = "true";
//            //判断线索状态
//            $status = $this->checkStatus($id);
//            $key = new KeyModel();
//            //获取线索详情
//            $keyDetail = $this->getKey($id);
//
//            //该线索积分
//            $credit =   $keyDetail['credit'];
//
//            $bought = array();
//            $bought['user_openid'] = $user_openid;
//            $bought['key_id'] = $id;
//            //b1 购买 b1 判断
//            if($status == 11){
//                //b1 判断为有效
//                if($result == "true"){
//                    $bought_key_status = 1;
//                    $data['status'] =100;
//                    $data['judge_status'] = 1;
//                    $data['b1_agree_time'] = date("Y-m-d H:i:s",time());
//
//                    //线索被判断为有效时，对线索发布者进行积分奖励
//                    $data['use_credit'] = 5;
//                    $data['frozen_credit'] = 0;
//                    $condition['user_openid'] = $keyDetail['user_openid'];
//                    changeCredit($condition,$data);
//
//                    //将积分分给线索发布者
//                    $data['use_credit'] = $credit;
//                    $data['frozen_credit'] = 0;
//                    $condition['user_openid'] = $keyDetail['user_openid'];
//                    changeCredit($condition,$data);
//
//                    //线索发布者积分增加
//                    $description = "您有一条线索被确认有效，增加积分";
//                    $credit_data = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "credit" => "+10",
//                        "title" => $description,
//                        "description" => $description,
//                        "create_time" => date("Y-m-d H:i:s",time())
//                    );
//                    addCreditLog($credit_data);
//
//                    //如果线索判断为有效，同时将购买者的积分解冻
//                    $data['use_credit'] = 0;
//                    $data['frozen_credit'] = -$credit;
//                    $condition['user_openid'] = $user_openid;
//                    changeCredit($condition,$data);
//
//                    //对购买者积分解冻扣除
//                    $description = "您将一条线索被确认有效，扣除积分";
//                    $credit_data = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "credit" => "-10",
//                        "title" => $description,
//                        "description" => $description,
//                        "create_time" => date("Y-m-d H:i:s",time())
//                    );
//                    addCreditLog($credit_data);
//
//                    //对线索发布者 进行经验奖励
//                    $condition['user_openid'] = $keyDetail['user_openid'];
//                    $jifen = 10;
//                    changeJifen($condition,$jifen);
//
//                    //统计当月线索被判断为有效的次数
//                    $count = M("cj_key")->where("user_openid = '".$keyDetail['user_openid']."' and status >=100 and status <999 and create_time >= ".date("Y-m",time()))->count("id");
//                    if($count >= 10){
//                        //对线索发布者进行经验奖励
//                        $condition['user_openid'] = $keyDetail['user_openid'];
//                        $jifen = 30;
//                        changeJifen($condition,$jifen);
//
//                        //对线索发布者进行积分奖励
//                        $data['use_credit'] = 20;
//                        $data['frozen_credit'] = 0;
//                        $condition['user_openid'] = $keyDetail['user_openid'];
//                        changeCredit($condition,$data);
//                    }
//                    $message = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "url"   => U('/addon/Chengjiaojia/Key/publishKeyDetail/id/'.$id),
//                        "first" => "您有一条线索被采纳",
//                        "keyword1"=>"线索被采纳",
//                        "keyword2" => date("Y-m-d H:i:s",time())
//                    );
//                    sendNoticeMessage($message);
//
//                    //线索发布者有效线索数加1
//                    M("cj_member")->where("user_openid = '".$keyDetail['user_openid']."'")->setInc("useful_keys");
//
//                }
//                //更新线索状态
//                if($key ->changeStatus($id,$data)){
//                    //添加用户判断记录
//                    $data['key_id'] = $id;
//                    $data['judge_openid'] = $user_openid;
//                    $data['judge_level'] ="5";
//                    $data['judge_reason'] = "线索有效";
//                    $data['judge_time'] = date("Y-m-d H:i:s",time());
//                    $key->addJudgeLog($data);
//
//                    //更新购买线索的状态
//                    $key->updateBoughtKey($bought,$bought_key_status);
//                    echo 1;
//                }
//                else{
//                    echo -1;
//                }
//            }
//            else if($status == 12){
//                //b2判断
//                if($result == "true"){
//                    $bought_key_status = 1;
//                    $data['status'] =200;
//                    $data['b2_agree_time'] = date("Y-m-d H:i:s",time());
//
//                    //如果线索判断为有效，同时将购买者的积分解冻
//                    $credit =   $keyDetail['credit'];
//                    $data['use_credit'] = 0;
//                    $data['frozen_credit'] = -$credit;
//                    $condition['user_openid'] = $user_openid;
//                    changeCredit($condition,$data);
//                    //将积分分给线索发布者
//                    $data['use_credit'] = $credit;
//                    $data['frozen_credit'] = 0;
//                    $condition['user_openid'] = $keyDetail['user_openid'];
//                    changeCredit($condition,$data);
//
//                    //线索被判断为有效时，对线索发布者进行积分奖励
//                    $data['use_credit'] = 5;
//                    $data['frozen_credit'] = 0;
//                    $condition['user_openid'] = $keyDetail['user_openid'];
//                    changeCredit($condition,$data);
//
//                    //对线索发布者 进行经验奖励
//                    $condition['user_openid'] = $keyDetail['user_openid'];
//                    $jifen = 10;
//                    changeJifen($condition,$jifen);
//
//                    //线索发布者积分增加
//                    $description = "您有一条线索被确认有效，增加积分";
//                    $credit_data = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "credit" => "+10",
//                        "title" => $description,
//                        "description" => $description,
//                        "create_time" => date("Y-m-d H:i:s",time())
//                    );
//                    addCreditLog($credit_data);
//
//                    //对购买者积分解冻扣除
//                    $description = "您将一条线索被确认有效，扣除积分";
//                    $credit_data = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "credit" => "-10",
//                        "title" => $description,
//                        "description" => $description,
//                        "create_time" => date("Y-m-d H:i:s",time())
//                    );
//                    addCreditLog($credit_data);
//                    //统计当月线索被判断为有效的次数
//                    $count = M("cj_key")->where("user_openid = '".$keyDetail['user_openid']."' and status >=100 and status <999 and create_time >= ".date("Y-m",time()))->count("id");
//                    if($count >= 10){
//                        //对线索发布者进行经验奖励
//                        $condition['user_openid'] = $keyDetail['user_openid'];
//                        $jifen = 30;
//                        changeJifen($condition,$jifen);
//
//                        //对线索发布者进行积分奖励
//                        $data['use_credit'] = 20;
//                        $data['frozen_credit'] = 0;
//                        $condition['user_openid'] = $keyDetail['user_openid'];
//                        changeCredit($condition,$data);
//                    }
//
//                    M("cj_member")->where("user_openid = '".$keyDetail['user_openid']."'")->setInc("useful_keys");
//                    $message = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "url"   => U('/addon/Chengjiaojia/Key/publishKeyDetail/id/'.$id),
//                        "first" => "您有一条线索被采纳",
//                        "keyword1"=>"线索被采纳",
//                        "keyword2" => date("Y-m-d H:i:s",time())
//                    );
//                    sendNoticeMessage($message);
//                }
//
//                //更新线索状态
//                if($key ->changeStatus($id,$data)){
//                    //添加用户判断记录
//                    $data['key_id'] = $id;
//                    $data['judge_openid'] = $user_openid;
//                    $data['judge_level'] ="5";
//                    $data['judge_reason'] = "线索有效";
//                    $data['judge_time'] = date("Y-m-d H:i:s",time());
//                    $key->addJudgeLog($data);
//
//                    $key->updateBoughtKey($bought,$bought_key_status);
//
//                    echo 1;
//                }
//                else{
//                    echo -1;
//                }
//            }
//            else if($status == 13){
//                //b3判断
//                if($result == "true"){
//                    $bought_key_status = 1;
//                    $data['status'] =300;
//                    $data['b2_agree_time'] = date("Y-m-d H:i:s",time());
//                    $data['judge_status'] = 1;
//
//                    //如果线索判断为有效，同时将购买者的积分解冻
//                    $credit =   $keyDetail['credit'];
//                    $data['use_credit'] = 0;
//                    $data['frozen_credit'] = -$credit;
//                    $condition['user_openid'] = $user_openid;
//                    changeCredit($condition,$data);
//
//                    //将积分分给线索发布者
//                    $data['use_credit'] = $credit;
//                    $data['frozen_credit'] = 0;
//                    $condition['user_openid'] = $keyDetail['user_openid'];
//                    changeCredit($condition,$data);
//
//                    //线索被判断为有效时，对线索发布者进行积分奖励
//                    $data['use_credit'] = 5;
//                    $data['frozen_credit'] = 0;
//                    $condition['user_openid'] = $keyDetail['user_openid'];
//                    changeCredit($condition,$data);
//
//                    //对线索发布者 进行经验奖励
//                    $condition['user_openid'] = $keyDetail['user_openid'];
//                    $jifen = 10;
//                    changeJifen($condition,$jifen);
//
//                    //线索发布者积分增加
//                    $description = "您有一条线索被确认有效，增加积分";
//                    $credit_data = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "credit" => "+10",
//                        "title" => $description,
//                        "description" => $description,
//                        "create_time" => date("Y-m-d H:i:s",time())
//                    );
//                    addCreditLog($credit_data);
//
//                    //对购买者积分解冻扣除
//                    $description = "您将一条线索被确认有效，扣除积分";
//                    $credit_data = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "credit" => "-10",
//                        "title" => $description,
//                        "description" => $description,
//                        "create_time" => date("Y-m-d H:i:s",time())
//                    );
//                    addCreditLog($credit_data);
//                    //统计当月线索被判断为有效的次数
//                    $count = M("cj_key")->where("user_openid = '".$keyDetail['user_openid']."' and status >=100 and status <999 and create_time >= ".date("Y-m",time()))->count("id");
//                    if($count >= 10){
//                        //对线索发布者进行经验奖励
//                        $condition['user_openid'] = $keyDetail['user_openid'];
//                        $jifen = 30;
//                        changeJifen($condition,$jifen);
//
//                        //对线索发布者进行积分奖励
//                        $data['use_credit'] = 20;
//                        $data['frozen_credit'] = 0;
//                        $condition['user_openid'] = $keyDetail['user_openid'];
//                        changeCredit($condition,$data);
//                    }
//
//                    //线索发布者被评为有效的线索条数+1
//                    M("cj_member")->where("user_openid = '".$keyDetail['user_openid']."'")->setInc("useful_keys");
//
//                    //给线索发布者发送模板消息
//                    $message = array(
//                        "user_openid" => $keyDetail['user_openid'],
//                        "url"   => U('/addon/Chengjiaojia/Key/publishKeyDetail/id/'.$id),
//                        "first" => "您有一条线索被采纳",
//                        "keyword1"=>"线索被采纳",
//                        "keyword2" => date("Y-m-d H:i:s",time())
//                    );
//                    sendNoticeMessage($message);
//                }
//
//                //更新线索状态
//                if($key ->changeStatus($id,$data)){
//                    //添加用户判断记录
//                    $data['key_id'] = $id;
//                    $data['judge_openid'] = $user_openid;
//                    $data['judge_level'] ="5";
//                    $data['judge_reason'] = "线索有效";
//                    $data['judge_time'] = date("Y-m-d H:i:s",time());
//                    $key->addJudgeLog($data);
//
//                    $key->updateBoughtKey($bought,$bought_key_status);
//
//                    echo 1;
//                }
//                else{
//                    echo -1;
//                }
//            }
//            else{
//                echo -2;
//            }
//        }
        //确认购买页
        public function confirm(){
            //获取线索编号
            $id = $_GET['id'];
            $this->assign("id",$id);
            //获取线索相关信息
            //获取线索价值
            $data['id'] = $id;
            $value = M("cj_key")->where($data)->getField("money");
            $this->assign("value",$value);
            //获取用户账户余额
            $condition['user_openid'] = $this->user_openid;
            //判断用户是否设置交易密码
            $pwd = M("cj_member")->where($condition)->getField("pwd");
            if(isset($pwd) && !empty($pwd)){
                $this->assign("pwdStatus",1);
            }else{
                $this->assign("pwdStatus",0);
            }
            $money = M("cj_member")->where($condition)->getField("use_account");
            $this->assign("money",$money);

            if($money >= $value ){
                //当账户余额大于线索价值时
                $this->show(CUSTOM_TEMPLATE_PATH."/Key/buy_confirm.html");
            }else{
                $this->show(CUSTOM_TEMPLATE_PATH."/Key/buy_overdraft.html");
            }
        }

        //金额不足
        public function noMoney(){
            $this->show(CUSTOM_TEMPLATE_PATH."/Key/buy_no_money.html");
        }
        public function overdraft(){
            $this->show(CUSTOM_TEMPLATE_PATH."/Key/buy_overdraft.html");
        }

        //购买者对线索进行评价
        public function judgeResult(){
            $id = $_GET['id'];
            $this->assign("id",$id);
            $this->show(CUSTOM_TEMPLATE_PATH."/Key/key_result.html");
        }
        function test(){
            $str = 'a:6:{s:6:"luoche";s:5:"28.68";s:6:"gouzhi";s:5:"24500";s:7:"shangye";s:4:"6781";s:8:"chechuan";s:0:"";s:9:"jiaoqiang";s:3:"950";s:8:"shangpai";s:4:"1500";}';
            $str = unserialize($str);
            var_dump($str);
        }

        public function recharge(){

        }
        /*
         * 验证用户交易密码
         * */
        private  function checkPwd($para){
            $data['user_openid'] = $this->user_openid;
            $pwd = M("cj_member")->where($data)->getField("pwd");
            if(md5($para) === $pwd){
                return true;
            }else{
                return false;
            }

        }
        /*
         * 线索购买者将线索判断为无效时，线索发布者将会对该判断进行处理。
         * */

        public function keyTreatment(){
            $id = $_GET['id'];
            $this->assign("id",$id);
            $this->show(CUSTOM_TEMPLATE_PATH."/Key/key_treatment.html");
        }

        public function treat()
        {
            $id = $_POST['id'];
            /*
             * 当购买者也确认无效时
             * 1、该线索经过两次购买后，确定无效
             * 2、该线索第一次购买时，用户确认后，自行操作，是否继续发布或者关闭该线索
             *
             * 当购买者对评价有异议时，可进行申诉
             * */
            $result = $_POST['result'];
            $user_openid = $this->user_openid;

            //查询线索详情
            $keyDetail = M("cj_key")->where("id = " . $id)->find();
            $judge_status = $_POST['judge_status'];

            //当a确认线索无效时
            if ($judge_status == 1) {
                //判断线索状态
                $status = $this->checkStatus($id);
                $money = $keyDetail['money'];

                $key = new KeyModel();

                //当b1判断无效时
                if ($status == -1) {
                    $data['status'] = -100;
                    $condition['id'] = $id;
//                    $key->changeStatus($id, $data);
//                    $result = M("cj_key")->where($condition)->save($data);
                    $result = true;
                    if ($result) {
                        //对线索橙蕉豆进行处理
                        //线索评价为无效时，将购买者冻结积分解冻
                        $condition1['user_openid'] = $keyDetail['b1_openid'];
                        $data['use_account'] = (int)$money;
                        $data['frozen_account'] = -$money;
                        $result = changeAccount($condition1,$data);
                        //添加橙蕉豆变化日志
                        $description = "您将一条线索确认为无效，返还橙蕉豆";
                        $account_data = array(
                            "user_openid" => $keyDetail['b1_openid'],
                            "account" => $money,
                            "remark" => "+" . $money,
                            "desc" => $description,
                            "type" => 4,
                            "status" => 1,
                            "create_time" => date("Y-m-d H:i:s", time())
                        );
                        addAccountLog($account_data);
                        echo 1;
                        exit;
                    }else{
                        echo -1;
                        exit;
                    }
                }
                else if ($status == -2) {
                    $data['status'] = -200;
                    $condition['id'] = $id;

                    $key->changeStatus($id, $data);
                    $result = M("cj_key")->where($condition)->save($data);
                    if ($result) {
                        //对线索橙蕉豆进行处理
                        //线索评价为无效时，将购买者冻结积分解冻
                        $condition['user_openid'] = $keyDetail['b2_openid'];
                        $data['use_account'] = +$money;
                        $data['frozen_account'] = -$money;
                        changeAccount($condition,$data);

                        //添加橙蕉豆变化日志
                        $description = "您将一条线索确认为无效，返还橙蕉豆";
                        $account_data = array(
                            "user_openid" => $keyDetail['b2_openid'],
                            "account" => $money,
                            "remark" => "+" . $money,
                            "desc" => $description,
                            "type" => 4,
                            "status" => 1,
                            "create_time" => date("Y-m-d H:i:s", time())
                        );
                        addAccountLog($account_data);
                    }
                    echo 1;
                    exit;
                }
            }

            //当a进行申诉时
            else if ($judge_status == 2) {
                $status = $this->checkStatus($id);
                if($status == -1){
                    $sta = -11;
                }else if($status == -2){
                    $sta = -12;
                }
                $rst = $this->appeal($id,$sta);
                if($rst){
                    echo 2;
                }else{
                    echo -1;
                }
            }
        }

        /*
         * a 对 b 的评价做出处理
         * 如果a确认通过线索失效，则将对线索进行进一步处理（关闭线索或者编辑线索重新发布）同时，b购买线索时，冻结的橙蕉币会解冻
         * 如果a对结果进行申诉，则会对该线索进行标记，后台会进行审核
         * */
        private function appeal($id,$status){
            //设置线索状态为申诉状态
            $data['appeal'] = 1;
            $data['status'] = $status;
            $con['id'] = $id;
            $result = M("cj_key")->where("id = ".$id)->save($data);

            if($result){
                //插入申诉记录
                $appeal['user_openid'] = $this->user_openid;
                $appeal['key_id'] = $id;
                $appeal['status'] = 0;
                $appeal['create_time'] = date("Y-m-d H:i:s",time());
                return M("cj_appeal")-> add($appeal);

            }
        }

        //无效线索管理
        public function manage(){
            $id = $_GET['id'];
            $this->assign("id",$id);
            $result = M("cj_key")->where("id = ".$id)->find();
            $this->assign("result",$result);
            $this->display(CUSTOM_TEMPLATE_PATH."/Key/manage.html");
        }
        public function manageSave(){
            $condition['id'] = $_GET['id'];
            $status = $this->checkStatus($_GET['id']);
            $data = $_POST;
            if($status == -100){
                //该线索第一次被判为无效时
                $data['status'] = 1;
            }else if($status == -200){
                //当线索第二次被判为无效时
                $data['status'] =2;
            }else{
                //否则修改无效
                echo -1;exit;
            }
            if(M("cj_key")->where($condition)->save($data)){
                echo 1;exit;
            }else{
                echo -1;exit;
            }
        }


    }
?>