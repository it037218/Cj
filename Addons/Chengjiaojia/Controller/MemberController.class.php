<?php

    /*
     * 用户个人中心
     * 
     * */

    namespace Addons\Chengjiaojia\Controller;
    use Addons\Chengjiaojia\Controller\BaseController;
    use Addons\Chengjiaojia\Model\MemberModel;
    use Addons\Chengjiaojia\Model\KeyModel;
    use Addons\Chengjiaojia\Model\SystemModel;
    use Addons\Chengjiaojia\Model\MessageModel;
    use Addons\Chengjiaojia\Model\MallModel;
    use Think\Model;
    use User\Api\UserApi;
    use \Org\Util;
    class MemberController extends BaseController{
        private $user_openid;
        public function __construct(){
            parent::__construct();
            if(isset($_GET['useropenid']) && !empty($_GET['useropenid'])){
                $this->share();
                return true;
            }
            //获取用户openid
//            if(isset($_GET['test']) && $_GET['test'] == 1){
//                //超
//                $this->user_openid = "o0nM4txt6C1J0s-QYOPu8utZZ__I";
//                $_SESSION['user_openid'] = $this->user_openid;
//            }else if(isset($_GET['test']) && $_GET['test'] == 2){
//                //朱
//                $this->user_openid = "o0nM4t4nSz5FDTJERyVctDjrylKw";
//                $_SESSION['user_openid'] = $this->user_openid;
//            }else if(isset($_GET['test']) && $_GET['test'] == 3){
//                //伟
//                $this->user_openid = "o0nM4tzlrljX1Co_zqJ9Z1sKYGWg";
//                $_SESSION['user_openid'] = $this->user_openid;
//            }else if(isset($_GET['test']) && $_GET['test'] == 4){
//                //李
//                $this->user_openid = "o0nM4t1lM4_rqfTIHeusQZ5Pm1Y8";
//                $_SESSION['user_openid'] = $this->user_openid;
//            }else if(isset($_GET['test']) && $_GET['test'] == 5){
//                //橙蕉
//                $this->user_openid = "o0nM4t2H1jq4bzZdCnVtYyNLoltU";
//                $_SESSION['user_openid'] = $this->user_openid;
//            }else if(isset($_GET['test']) && $_GET['test'] == 6){
//                //季
//                $this->user_openid = "o0nM4tza3aoVJ6FsMUg4BlYAQG-I";
//                $_SESSION['user_openid'] = $this->user_openid;
//            }else

            if(isset($_GET['openid']) && !empty($_GET['openid'])){
                $this->user_openid = $_GET['openid'];
                $_SESSION['user_openid'] = $_GET['openid'];
            }else if(isset($_GET['id']) && !empty($_GET['id'])){
                $this->user_openid = $_GET['id'];
                $_SESSION['user_openid'] = $_GET['id'];
            }else {
                if(isset($_SESSION['user_openid']) && !empty($_SESSION['user_openid'])){
                    $data['user_openid'] = $_SESSION['user_openid'];
                }else  if(isset($_GET['openid'])  && $_GET['openid'] != ""){
                    $this->user_openid = $_GET['openid'];
                    $data['user_openid'] = $_GET['openid'];
                    $data['nickname'] = $_GET['nickname'];
                    $data['headimgurl'] = $_GET['headimgurl'];
                    $_SESSION['user_openid'] = $_GET['openid'];
                    //检查用户是否注册
                    check_register($data);
//                    $_SESSION['user_openid'] = $data['user_openid'];
                }else{
                    $data = get_wx_id();
                }
                $this->user_openid = $data['user_openid'];
                if(!isset($_GET['opt'])){
                    $m = new MemberModel();
                    $status = $m->check($data['user_openid']);

                    $con['user_openid'] = $this->user_openid;
                    $realStatus = M("cj_member")->where($con)->getField("realStatus");

                    if($status < 4 && $realStatus <1){
                        $this->check_reg($status);
                        exit;
                    }
                }
            }

        }

        public function get(){

        }

        public function checkStatus($user_openid){
            $m = new MemberModel();
            $status = $m->check($user_openid);
            return $status;
        }

        /*
           * 信用
           * 有效线索数/总共被购买的线索数
           *
         * */
        public function getUsefulRate(){
            //获取用户总共有效的线索数
            $key1 = $this->getUsefulKeys();

            //获取用户总共被购买的线索条数
            $key2 = $this->getUsedKeys();

            //计算线索有效率
            $rate = $key2['num']==0?"5":$key1['num']/$key2['num']*5;
            return $rate;
        }
        /*
         *实际成交率
         * 晒单数/被购买的线索数
         *
         * */

        private function getActRate(){
            $key1 = $this->getUsefulKeys();
            $key2 = $this->getActKeys();
            //计算线索有效率
            $rate = $key2['num']==0?"100":$key1['num']/$key2['num'];
            return $rate;
        }


        /*
         * 获取用户发布线索总条数
         * */
        private function getUserKeys(){
            $key = new KeyModel();
            $user_openid = $this->user_openid;
            return $key->getUserKeys($user_openid);
        }

        /*
         * 获取用发布的被购买线索条数（被购买）
         * */
        private function getUsedKeys(){
            $data['user_openid'] = $this->user_openid;
            return M('cj_member')->where($data)->getField("bought_keys");
        }

        /*
         * 获取用户有效线索条数(被判为有效)
         * */

        private function getUsefulKeys(){
            $data['user_openid'] = $this->user_openid;
            return M('cj_member')->where($data)->getField("useful_keys");
        }
        /*
         * 获取晒单数量
         * */
        private function getActKeys(){
           $data['user_openid'] = $this->user_openid;
            return M('cj_member')->where($data)->getField("shared_keys");
        }


        /*
         * 获取用户未读消息条数
         * */
        private function getUnreadNum(){
            $message = new MessageModel();
            $user_openid = $this->user_openid;
            return $message->getUnreadNum($user_openid);
        }

        /*
         * 个人中心页
         * */
        public function ucenter(){

            //获取用户基本信息

            $m = new MemberModel();
            $user_openid = $this->user_openid;
            $userInfo = $m->getOne($user_openid);


            //判断用户是否已经实名认证
            $realStatus = $userInfo['realStatus'];
//            if($realStatus == 0){
//                $this->realCheck();
//                exit;
//            }

            $this->assign("realStatus",$realStatus);

            //判断用户是否已经创建交易密码
            if($userInfo['pwd'] != null && !empty($userInfo['pwd'])){
                $pwdStatus = 1;
            }else{
                $pwdStatus = 0;
            }
            $this->assign("pwdStatus",$pwdStatus);

            $this->assign("userInfo",$userInfo);

            $data['user_openid'] = $userInfo['user_openid'];

            //获取用户线索有效率
            $keyUsefulRate = $userInfo['useful_keys']/$userInfo['bought_keys']*5;
            $this->assign("rate",$keyUsefulRate);

            //获取用户实际成交率
            $keyActRate = $this->getActRate();
            $this->assign("actRate",$keyActRate);
            /*
             * 获取未读消息数量
             * */
            $unReadNum = 0;
            $unReadNum = $this->getUnreadNum();
            $this->assign("messageNum",$unReadNum);

            //积分商城产品
            $m = new MallModel();
            $result = $m -> getCommodityList();
            $this->assign("commodity",$result);

            //判断用户今日是否已经签到
            $sign = 1;
            if($userInfo['sign_time'] >= date("Y-m-d",time())){
                $sign = 0;
            }
            $this->assign("sign",$sign);

            //判断用户今日是否已经发布线索
            $publish = 0;
            $keyCon['user_openid'] = $this->user_openid;
            $keyCon['create_time'] = array(
                "gt",date("Y-m-d",time())
            );
            $key = M("cj_key")->where($keyCon)->find();
            if(!empty($key)){
                $publish = 1;
            }
            $this->assign("publish",$publish);

            //判断用户今日是否已近邀请会员
//            $invite = 0;
            $inviteCon['create_time'] = array("egt",date("Y-m-d",time()));
            $inviteCon['status'] = 3;
            $inviteCon['recommender'] = $this->user_openid;
            $result = M("cj_member")->where($inviteCon)->find();
            if(!empty($result)){
                $invite = 1;
            }else{
                $invite = 0;
            }
            $this->assign("invite",$invite);

            //判断用户今日是否晒单
            $shareCon['create_time'] = array("egt",date("Y-m-d",time()));
            $shareCon['user_openid'] = $this->user_openid;
            $shareRst = M("cj_share_order")->where($shareCon)->find();
            if(!empty($shareRst)){
                $share = 1;
            }else{
                $share = 0;
            }
            $this->assign("share",$share);
            $this->show(CUSTOM_TEMPLATE_PATH."/Member/ucenter.html");
        }

        /*
         * 查看个人积分详情
         *
         * */
        public function credit(){
            $user_openid = $this->user_openid;
            $m = new MemberModel();
            //获取用户当前剩余积分
            $user_info = $m -> getMemberInfo($user_openid);
            $this->assign("credit",$user_info['use_credit']);

            //获取用户积分明细
            $credit_info = $m->getCreditInfo($user_openid);
            $this->assign("result",$credit_info);
            $this->show(CUSTOM_TEMPLATE_PATH."/Member/credit.html");
        }

        /*
         * 进入个人等级详情页面
         * */

        public function level(){
            //获取用户当前等级
            $m = new MemberModel();
            $userInfo = $m -> getMemberInfo($this->user_openid);
            $this->assign("level",$userInfo['level']);
            $this->assign("jifen",$userInfo['jifen']);

            //获取用等级详情
            $this->show(CUSTOM_TEMPLATE_PATH."/Member/level.html");
        }

        /*
         * 获取用户个人信息
         * */
        public function index(){
            /*
             * 获取用户基本信息
             * user_openid
             * */
            //调用录音通话接口
//            $record = new \Org\Util\Record();
//            $record->GetAgentMoney();


            $m = new MemberModel();
            $userInfo = $m->getOne($this->user_openid);
            $this->assign("userInfo",$userInfo);
            $this->show(CUSTOM_TEMPLATE_PATH."/Member/profile.html");

        }

        /*
         * 个人信息编辑
         * */
        public function updateMember(){

            $user_openid = $this->user_openid;
            $data = $_POST;

            $m = new MemberModel();
            if($m -> updateMember($data,$user_openid)){
                echo 1;
            }else{
                echo -1;
            }
        }

        /*
         * 修改积分
         * */
        public function creditChange($res){
            $data['user_openid'] = $res["user_openid"];
            $data['use_credit'] = $res['use_credit'];                   //可用积分
            $data['frozen_credit'] = $res['frozen_credit'];            //冻结积分
            $member = new MemberModel();
            $result = $member->creditChange($data);
            return $result;
        }


        //进入消息列表页
        public function message(){
            //查看消息
            $data = array();
            $data['user_openid'] = $this->user_openid;
            $system = new MessageModel();
            $message=$system -> getMessageList($data);
            $this->assign("message",$message);
            $this->show(CUSTOM_TEMPLATE_PATH."/Member/message.html");
        }

        //获取消息列表
        public function getMessageList($data){
            $m = new SystemModel();
            $result =$m -> where("status=".$data['status'])->select();
            return $result;
        }

        //任务列表页面
        public function task(){
            $user_openid = $this->user_openid;
            //查询用户签到状态
            $info = M("cj_member")->where("user_openid = '".$this->user_openid."'")->find();
            if($info['sign_time']>date("Y-m-d",time())){
                $this->assign("data1",1);
            }else{
                $this->assign("data1",0);
            }
            //查询用户今日是否已经发布线索
            $data2['user_openid'] = $user_openid;
            $data2['create_time'] = array("egt",date("Y-m-d",time()));
            $rst2 = M("cj_key")->where($data2)->find();
            if(!empty($rst2)){
                $this->assign("data2",1);
            }else{
                $this->assign("data2",0);
            }
            //查询本月发布线索条数
            $data3['user_openid'] = $user_openid;
            $data3['create_time'] = array("egt",date("Y-m",time()));
            $rst3 = M("cj_key")->where($data3)->count();
            if($rst3 >=15){
                $this->assign("data3",1);
            }else{
                $this->assign("data3",0);
            }

            //查询本月晒发票次数
            $data4['user_openid'] = $user_openid;
            $data4['create_time'] = array("egt",date("Y-m",time()));
            $rst4 = M("cj_share_order")->where($data4)->count();
            if($rst4>=3){
                $this->assign("data4",1);
            }else{
                $this->assign("data4",0);
            }
            $this->show(CUSTOM_TEMPLATE_PATH."/Member/tasks.html");
        }

        //注册
        public function check_reg($status){
            if($status['status'] == 0){
                $this->reg1();
            }else if($status['status'] == 1 || $status['status'] == 5){
                $this->reg2();
            }else if($status['status'] == 2){
                $this->reg3();
            }else{
                $this->reg_result();
            }
        }

        /*
         * 注册页面1
         * */
        public function reg1(){
            $data['user_openid'] = $this->user_openid;
            //获取用户来源
            $from = M("cj_member")->where($data)->getField("from_scene_id");
            if(!empty($from) && $from>0){
                $this->assign('scene_id',$from);
                $this->show(CUSTOM_TEMPLATE_PATH."/Member/reg1_1.html");
            }else{
                $this->assign('scene_id',47);
                $this->show(CUSTOM_TEMPLATE_PATH."/Member/reg1_1.html");
            }
        }
        /*
         * 注册页面2
         * */
        public function reg2(){
            $host = "http://'".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->assign("host",$host);
            $m = new MemberModel();
            $userInfo = $m->getOne($this->user_openid);
            $this->assign("userinfo",$userInfo);
            $this->show(CUSTOM_TEMPLATE_PATH."/Member/reg2.html");
        }

        /*
         * 用户注册成功
         * */
        public function verify($user_openid){
            //用户认证成功
            $data['status'] = 3;
        }



        /*
         * 注册页面
         * */
        public function reg3(){
            $host = "http://'".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->assign("host",$host);
            $this->show(CUSTOM_TEMPLATE_PATH."/Member/reg3.html");
        }
        /*
         * 用户注册
         * */
        public function register(){
            $opt = $_GET['opt'];
            $user_openid = $this->user_openid;
            $m = new MemberModel();
            if($opt == "checkInfo"){
                //检验邀请码是否存在

                if(isset($_GET['status']) && $_GET['status'] == "scene"){
                    if(isset($_POST['telephone']) && !empty($_POST['telephone'])){
                        $data['telephone'] = $_POST['telephone'];
                    }
                }else{
                    $code = M("cj_member")->getFields("invite_code");
                    if(!in_array($_POST['code'],$code)) {
                        echo -1;
                        exit;
                    }else if(isset($_POST['telephone']) && !empty($_POST['telephone'])){
                        $data['telephone'] = $_POST['telephone'];
                    }
                }
            }
            //检查用户status
            $status = $this->checkStatus($user_openid);
            //根据邀请码获取用户信息
            if($status == 0){
                $data['status'] = 1;
                if(isset($_POST['code']) && $_POST['code'] != ""){
                    $data['recommender'] = M("cj_member")->where("invite_code = '".$_POST['code']."'")->getField("user_openid");
                }else if(isset($_GET['status']) && $_GET['status'] == "scene"){
                    $data['recommender'] = M("cj_member")->where("id= '".$_POST['scene_id']."'")->getField("user_openid");
                }

                $data['telephone'] = $_POST['telephone'];
            }else if($status == 1){
                $data = $_POST;
                $data['status'] = 2;
            }
            else if($status == 2){
                $data=$_POST;
                //获取用户id
                $rst = M("cj_member")->where("user_openid = '".$this->user_openid."'")->getField("id");
                $str = "shcj".sprintf("%04d",$rst['id']);
                $data['invite_code'] = $str;
                $data['status'] = 3;
                $data['use_credit'] = 20;
//                $this->verifyAuth($user_openid);

                $message['username'] = $_POST['name'];

//                sendVerifyMessage($message);
            }
            $result = $m->saveMember($data,$user_openid);
            if($result != false){
                echo 1;
                exit;
            }else{
                echo -4;
                exit;
            }
        }
        /*
         * 审核结果
         * */
        public function reg_result(){
            $status = $this->checkStatus($this->user_openid);
            $this->assign("status",$status);

//            //查询改城市品牌的剩余名额
//            $m = new MemberModel();
//            $user_openid = $this->user_openid;
//            $userInfo = $m->getOne($user_openid);
//            $this->assign("city",$userInfo['city']);
//
//            //豪华品牌
//            $data['city_name'] = $userInfo['city'];
//            $data['good'] = 1;
//            $result['good'] = M('cj_register_brand_number')->where($data)->select();
//            foreach($result['good'] as $k=> $v){
//                $result['good'][$k]['remain'] = $v['total'] - $v['realtime_number'];
//            }
//            $data['good'] = 0;
//            $result['normal'] = M('cj_register_brand_number')->where($data)->select();
//            foreach($result['normal'] as $k=> $v){
//                $result['normal'][$k]['remain'] = $v['total'] - $v['realtime_number'];
//            }
//            $this->assign("result",$result);
//            echo $status;exit;
            $this->show(CUSTOM_TEMPLATE_PATH."/Member/reg_result.html");
        }
        /*
         * 晒单
         * */

        public function shareOrder(){
            $this->show(CUSTOM_TEMPLATE_PATH."/Member/shareOrder.html");
        }

        public function createShare(){
            $post = array(
                "luoche"=>$_POST['luoche'],
                "gouzhi"=>$_POST['gouzhi'],
                "shangye" => $_POST['shangye'],
                "chechuan" => $_POST['chechuan'],
                "jiaoqiang" => $_POST['jiaoqiang'],
                "shangpai" => $_POST['shangpai']
            );
            $data['content'] = serialize($post);
            $data['create_time'] = date("Y-m-d H:i:s",time());
            $data['user_openid'] = $this->user_openid;
            $data['key_id'] = $_POST['key_id'];
            $data['pic_url'] = $_POST['pic_url'];
            $data['car_type'] = $_POST['car_type'];
            $res = M("cj_share_order")->add($data);
            if($res>0){
                echo 1;
            }else{
                echo -1;
            }
        }
        /*
         *
         * 签到
         * */

        public function sign(){
            $user_openid = $this->user_openid;
            $m = new MemberModel();
            $info = $m->getOne($user_openid);
            if($info['sign_time'] > date("Y-m-d",time())){
                //表示今日已经签到
                echo -1;
                exit;
            }else{
                $data['use_credit'] = 15;
                $data['frozen_credit'] = 0;

                $condition['user_openid'] = $user_openid;
                if(changeCredit($condition,$data)){
                    $description = "每日签到";
                    $credit_data = array(
                        "user_openid" => $user_openid,
                        "credit" => "+15",
                        "title" => $description,
                        "description" => $description,
                        "create_time" => date("Y-m-d H:i:s",time())
                    );
                    addCreditLog($credit_data);
                    //更新签到时间
                    $d['sign_time'] = date("Y-m-d H:i:s",time());
                    $m->saveMember($d,$user_openid);
                    echo 1;
                }
            }

        }

        /*
         * 邀请页（旧）
         * */
//        public function invite(){
//            //查询改城市品牌的剩余名额
//            $m = new MemberModel();
//            $user_openid = $this->user_openid;
//            $userInfo = $m->getOne($user_openid);
//            $this->assign("city",$userInfo['city']);
//
//            //豪华品牌
//            $data['city_name'] = $userInfo['city'];
//            $data['good'] = 1;
//            $result['good'] = M('cj_register_brand_number')->where($data)->select();
//            foreach($result['good'] as $key=>$value){
//                $result['good'][$key]['remain'] = $result['good'][$key]['total'] - $result['good'][$key]['realtime_number'];
//            }
//            $data['good'] = 0;
//            $result['normal'] = M('cj_register_brand_number')->where($data)->select();
//            foreach($result['normal'] as $key=>$value){
//                $result['normal'][$key]['remain'] = $result['normal'][$key]['total'] - $result['normal'][$key]['realtime_number'];
//            }
//            $data['good'] = 2;
//            $result['guochan'] = M('cj_register_brand_number')->where($data)->select();
//            foreach($result['guochan'] as $key=>$value){
//                $result['guochan'][$key]['remain'] = $result['guochan'][$key]['total'] - $result['guochan'][$key]['realtime_number'];
//            }
//            $this->assign("result",$result);
//            $m = new MemberModel();
//            $info = $m->getMemberInfo($this->user_openid);
//            $this->assign("invite_code",$info['invite_code']);
//
//            //抽奖次数
//            $user_openid = $this->user_openid;
//            //获取用户推荐好友人数
//            $con['recommender'] = $user_openid;
//            $result1 = M("cj_member")->where($con)->count();
//            $con1['user_openid'] = $user_openid;
//            $result2 = M("cj_member")->where($con1)->getField("reward_times");
//            $this->assign("num",$result1-$result2);
//
//            $this->show(CUSTOM_TEMPLATE_PATH."/Member/invite.html");
//        }


        /*
         * 邀请页
         * 2015-09-23
         * 朱云峰
         * */
        public function invite(){

            $user_openid = $this->user_openid;

            $data['user_openid'] = $user_openid;
            $qr_img = M("cj_member")->where($data)->getField("qr_pic");
            if(!empty($qr_img)){
                $this->assign("img",$qr_img);
            }else{
                $avatar = M("cj_member")->where($data)->getField("avatar");
                $id = M("cj_member")->where($data)->getField("id");

                $scene_id = $id;
                //获取用户二维码
                $result = getQRcode($avatar,$user_openid,$scene_id);
                $data1['qr_pic'] = $result;
                M("cj_member")->where($data)->save($data1);
                $this->assign("img",$result);
            }
            //获取用户昵称
            $name = M("cj_member")->where($data)->getField("name");
            $this->assign("name",$name);
            $code = M("cj_member")->where($data)->getField("invite_code");
            $this->assign("code",$code);
            //查找用户邀请好友数
            $con['create_time'] = array("gt","2015-09-24 00:00:00");
            $con['recommender'] = $user_openid;
            $count = M("cj_member")->where($con)->count();
            $this->assign("count",$count);

            $str = "/addon/Chengjiaojia/Member/share/useropenid/".$user_openid;
            $url = U("$str");
            $this->assign("url",$url);

            $this->show(CUSTOM_TEMPLATE_PATH."/Member/invite.html");

        }




        /*
         * 意见反馈
         * */
        public function suggestion(){
            $this->show(CUSTOM_TEMPLATE_PATH."/Member/suggestion.html");
        }

        /*
         * 保存意见反馈
         * */
        public function saveSuggestion(){
            $m = new MemberModel();
            $data['user_openid'] = $this->user_openid;
            $data['content'] = $_POST['content'];
            $data['create_time'] = date("Y-m-d H:i:s",time());
            //插入图片
            foreach($_POST['images'] as $key => $value){
                $k = $key + 1;
                $data['img'."$k"] = $value;
            }
            $rst = M("cj_suggestion")->add($data);
            if($rst>0){
                echo 1;
            }else{
                echo -1;
            }
        }

        /*
         * 用户认证
         * */

        public function verifyAuth($user_openid){
            $con['user_openid'] = $user_openid;
            $data['status'] = 3;
            $data['use_credit'] = 20;
            M('cj_member')->where($con)->save($data);
        }

        //进入抽奖页
        public function gift(){
            $user_openid = $this->user_openid;
            //获取用户推荐好友人数
            $con['recommender'] = $user_openid;
            $result1 = M("cj_member")->where($con)->count();
            $con1['user_openid'] = $user_openid;
            $result2 = M("cj_member")->where($con1)->getField("reward_times");
            $this->assign("num",$result1-$result2);
            $this->show(CUSTOM_TEMPLATE_PATH."/public/gift.html");
        }
        //抽奖结果记录
        public function reward(){
            $credit = $_POST['credit'];
            $user_openid = $this->user_openid;
            $con['user_openid'] = $user_openid;
            //检查用户抽剩余奖次数
            $con1['recommender'] = $user_openid;
            $result1 = M("cj_member")->where($con1)->count();
            $result2 = M("cj_member")->where($con)->getField("reward_times");

            if($result1-$result2>0){
                $result = M("cj_member")->where($con)->setInc("use_credit",$credit);
                //发送模板消息
                $message = array(
                    "user_openid" => $user_openid,
                    "url"   => U('/addon/Chengjiaojia/Member/gift'),
                    "first" => "恭喜您已抽奖成功！",
                    "keyword1"=>"恭喜您抽中".$credit."积分奖励",
                    "keyword2" => date("Y-m-d H:i:s",time())
                );
                sendNoticeMessage($message);

                //积分变化日志
                $description = "抽奖奖励";
                $credit_data = array(
                    "user_openid" => $user_openid,
                    "credit" => "+".$credit,
                    "title" => $description,
                    "description" => $description,
                    "create_time" => date("Y-m-d H:i:s",time())
                );
                addCreditLog($credit_data);

                //记录用户抽奖记录
                $reward['user_openid'] = $user_openid;
                $reward['content'] = $_POST['content'];
                $reward['create_time'] = date("Y-m-d H:i:s",time());
                M("cj_reward")->add($reward);

                //增加用户抽奖次数
                M("cj_member")->where($con)->setInc("reward_times");
                echo 1;
            }
            else{
                //抽奖次数不够
                echo -1;
            }


        }

        //个人资料修改页
        public function profile_edit(){
            $m = new MemberModel();
            $userInfo = $m->getOne($this->user_openid);
            $this->assign("userInfo",$userInfo);
            $this->show(CUSTOM_TEMPLATE_PATH."/Member/edit_info.html");
        }

        //签到转盘页
        public function signReward(){
            //检查用户今日是否已经签到
            $m = new MemberModel();
            $user_openid = $this->user_openid;
            $userInfo = $m->getOne($user_openid);
            if($userInfo['sign_time'] >= date("Y-m-d",time())){
                $sign = 0;
            }else{
                $sign = 1;
            }
            $this->assign("num",$sign);
            $this->assign("signStatus",$sign);
            $this->display(CUSTOM_TEMPLATE_PATH."/public/sign.html");
        }

        //生成抽奖结果
        public function createReward(){
             $num = floor(rand(1,10)*20)+1;
            $data = array();
            $data['num'] = "";
            if($num <= 170){
                $_SESSION["reward"] = 2;
                $data['num'] = 1;
            }else if($num>170 && $num <=180){
                $_SESSION["reward"] = 10;
                $data['num'] = 2;
            }else if($num>180 && $num <=200){
                $_SESSION["reward"] = 5;
                $data['num'] = 4;
            }
            $data['timestamp'] = time();
            $data['code'] = md5($data['timestamp'].$_SESSION["reward"]."cj");
            echo json_encode($data);
        }


        //签到转盘结果
        public function signResult(){
            if(empty($_POST['credit']) || empty($_POST['code']) || empty($_POST['content']) || empty($_POST['timestamp'])){
                $log['user_openid'] = $this->user_openid;
                $log['operation'] = "有嫌疑";
                $log['create_time'] = date("Y-m-d H:i:s",time());
                M("cj_log")->add($log);
                echo -4;
                exit;
            }
            $time = $_POST['timestamp'];
            if((time() - $time)>30){
                $log['user_openid'] = $this->user_openid;
                $log['operation'] = "有嫌疑";
                $log['create_time'] = date("Y-m-d H:i:s",time());
                M("cj_log")->add($log);
                echo -2;
                exit;
            }else{
                $num = $_POST['credit'];
                $code = $_POST['code'];
                $vCode = md5($time.$num."cj");
                if($vCode != $code || $num != $_SESSION["reward"]){
                    $log['user_openid'] = $this->user_openid;
                    $log['operation'] = "有嫌疑";
                    $log['create_time'] = date("Y-m-d H:i:s",time());
                    M("cj_log")->add($log);

                    echo -3;
                    exit;
                }else{
                    $credit = $_POST['credit'];
                    $user_openid = $this->user_openid;
                    //检查用户今日是否已经签到
                    $con['sign_time'] = array("egt",date("Y-m-d",time()));
                    $con['user_openid'] = $user_openid;
                    $result = M("cj_member")->where($con)->find();
                    if($result != null){
                        //表示今日已经签到
                        echo -1;
                        exit;
                    }else{
                        //当用户今日未签到时
                        $con1['user_openid'] = $user_openid;
                        //增加用户积分
                        $result = M("cj_member")->where($con1)->setInc("use_credit",$credit);
                        //更新签到时间
                        $m = new MemberModel();
                        $d['sign_time'] = date("Y-m-d H:i:s",time());
                        $m->saveMember($d,$user_openid);

                        //积分变化日志
                        $description = "每日签到,积分抽奖";
                        $credit_data = array(
                            "user_openid" => $user_openid,
                            "credit" => "+".$credit,
                            "title" => $description,
                            "description" => $description,
                            "create_time" => date("Y-m-d H:i:s",time())
                        );
                        addCreditLog($credit_data);
                        //发送模板消息
                        $message = array(
                            "user_openid" => $user_openid,
                            "url"   => U('/addon/Chengjiaojia/Member/gift'),
                            "first" => "恭喜您已抽奖成功！",
                            "keyword1"=>"恭喜您抽中".$credit."积分奖励",
                            "keyword2" => date("Y-m-d H:i:s",time())
                        );
                        sendNoticeMessage($message);
                        //记录用户抽奖记录
                        $reward['user_openid'] = $user_openid;
                        $reward['content'] = $_POST['content'];
                        $reward['create_time'] = date("Y-m-d H:i:s",time());
                        M("cj_reward")->add($reward);
                        echo 1;
                    }
                }

            }
        }

        public function account(){
            $openid = $this->user_openid;
            //获取用户当前可用橙蕉豆
            $data['user_openid'] = $openid;
            $pwd = M("cj_member")->where($data)->getField("pwd");
            if($pwd == "" || empty($pwd)){
                $url =  U('pwdCreate');
                header("Location:".$url);
//                exit;
            }else{
                $use_account = M("cj_member")->where($data)->getField("use_account");
                $this->assign("use_account",$use_account);
                //获取用户橙蕉豆使用明细
                $data['status'] = 1;
                $list = M("cj_account")->where($data)->order('create_time desc')->select();
                $this->assign("list",$list);
                $this->show(CUSTOM_TEMPLATE_PATH."/Member/account.html");
            }
        }
        function wxPay_notify(){

        }
        public function recharge(){
            $pref = $_SERVER['HTTP_REFERER'];
            $this->assign("href",$pref);
            $this->show(CUSTOM_TEMPLATE_PATH."/Member/recharge.html");
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
        public function cash(){
            $user_openid = $this->user_openid;
            //获取用户当前剩余橙蕉豆
            $use_account = M("cj_member")->where("user_openid = '".$user_openid."'")->getField("use_account");
            $this->assign("use_account",$use_account);

            //获取用户当月提现次数
            $condition['user_openid'] = $user_openid;
            $condition['create_time'] = array("egt",date("Y-m",time()));
            $cashCount = M("cj_cash")->where($condition)->count();
            $this->assign("num",3-(int)$cashCount);
            $this->show(CUSTOM_TEMPLATE_PATH."/Member/cash.html");
        }
        public function cashConfirm(){

            $user_openid = $this->user_openid;
            //验证密码
            $pwd = $_POST['pwd'];
            $result = $this->checkPwd($pwd);
//            $result = true;
            $account = $_POST['account'];
            if($result == false){
                echo -1;    //密码错误
                exit;
            }else{
                //验证用户金额
                $use_account = M("cj_member")->where("user_openid = '".$user_openid."'")->getField("use_account");

                if($account > $use_account){
                    echo -2;    //提现金额不足
                    exit;
                }else{
                    $condition['user_openid'] = $user_openid;
                    $condition['create_time'] = array("egt",date("Y-m",time()));
                    $cashCount = M("cj_cash")->where($condition)->count();

                    if($cashCount >=3){
                        echo -3;    //当月提现次数过多
                        exit;
                    }else{
                        $data['user_openid'] = $user_openid;
                        $data['cash_account'] = $account;
                        $data['status'] = 0;
                        $data['create_time'] = date("Y-m-d H:i:s",time());
                        if(M("cj_cash")->add($data)){
                            //将用户的可用金额冻结
                            $accountData['use_account'] = -$account;
                            $accountData['frozen_account'] = -$account;
                            $data1['user_openid'] = $user_openid;
                            if(changeAccount($data1,$accountData)){
                                echo 1;
                                exit;
                            }
                        }else{
                            echo -4;exit;
                        }
                    }
                }
            }
        }

        //修改密码，校验原密码的准确性
        public function checkPrePwd(){
            $data['pwd'] = md5(trim($_POST['pwd']));
            $data['user_openid'] = $this->user_openid;
            if(M('cj_member')->where($data)->find()){
                echo 1;exit;
            }else{
                echo -1;exit;
            }
        }

        public function pwdCreate(){
            if(isset($_GET['opt']) && !empty($_GET['opt']) && $_GET['opt'] == "change"){
                $text = "请输入新的橙蕉平台支付密码";
            }else{
                $text = "请创建橙蕉平台支付密码";
            }

            $this->assign("text",$text);
            $this->display(CUSTOM_TEMPLATE_PATH."/Member/pwd_create.html");
        }
        public function pwdConfirm(){

            $this->display(CUSTOM_TEMPLATE_PATH."/Member/pwd_confirm.html");
        }
        public function pwdEdit(){
            $this->display(CUSTOM_TEMPLATE_PATH."/Member/pwd_edit.html");
        }

        //保存密码
        public function pwdSave(){
            $pwd = $_POST['pwd'];
            if(isset($pwd) && !empty($pwd)){
                $pwd = md5($pwd);
            }
            $condition['user_openid'] = $this->user_openid;
            $data['pwd']  = $pwd;
            if(M("cj_member")->where($condition)->save($data)){
                echo 1;
            }else{
                echo -1;
            }
        }

        //修改个人资料
        public function editInfo(){
            $m = new MemberModel();
            $userInfo = $m->getOne($this->user_openid);
            $userInfo['card_number'] = substr_replace($userInfo['card_number'],"********",8,8);
            $this->assign("userInfo",$userInfo);
            $this->display(CUSTOM_TEMPLATE_PATH."/Member/edit_info.html");
        }

        //修改昵称
        public function editName(){
            $this->assign("href",$_SERVER['HTTP_REFERER']);
            $this->display(CUSTOM_TEMPLATE_PATH."/Member/edit_name.html");
        }
        //修改电话
        public function editTel(){
            $this->assign("href",$_SERVER['HTTP_REFERER']);
            $this->display(CUSTOM_TEMPLATE_PATH."/Member/edit_tel.html");
        }

        //保存修改个人资料
        public function editSave(){
            $data['user_openid'] = $this->user_openid;
            if(M("cj_member")->where($data)->save($_POST)){
                echo 1;
            }else{
                echo -1;
            }
        }

        //提交用户资料修改审核
        public function editApply(){
            $data = $_POST;
            $data['status'] = 0;
            $data['user_openid'] = $this->user_openid;
            $data['create_time'] = date("Y-m-d H:i:s",time());
            unset($data['realStatus']);
            if(M('cj_info_edit')->add($data)){
                $res['realStatus'] = 2;
                $con['user_openid'] = $this->user_openid;
                M("cj_member")->where($con)->save($res);

                echo 1;
            }else{
                echo -1;
            }
        }
        public function cashResult(){
            $this->display(CUSTOM_TEMPLATE_PATH."/Member/cash_success.html");
        }

        //实名认证页
        public function realCheck(){
            $this->display(CUSTOM_TEMPLATE_PATH."/Member/realCheck.html");
        }

        public function share(){
            if(isset($_GET['useropenid']) && !empty($_GET['useropenid'])){
                //获取用户相关信息
                $data['user_openid'] = $_GET['useropenid'];
                $result = M("cj_member")->where($data)->find();
                $this->assign("img",$result['qr_pic']);
                $this->assign("code",$result['invite_code']);
                $this->assign("name",$result['name']);
            }
            $this->display(CUSTOM_TEMPLATE_PATH."/Member/share.html");
        }



    }
?>