<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2015/6/21
 * Time: 19:03
 * 悬赏模块
 */

    namespace Addons\Chengjiaojia\Controller;
    use Addons\Chengjiaojia\Controller\BaseController;
    use Addons\Chengjiaojia\Model\KeyModel;
    use Addons\Chengjiaojia\Model\MemberModel;
    use Addons\Chengjiaojia\Model\WantedModel;
    use Think\Model;
    class WantedController extends BaseController{
        private $user_openid;

        public function __construct(){
            parent::__construct();
//            if(isset($_SESSION['user_openid']) && !empty($_SESSION['user_openid'])){
//                $data['user_openid'] = $_SESSION['user_openid'];
//            }else  if(isset($_GET['openid'])  && $_GET['openid'] != ""){
//                $data['user_openid'] = $_GET['openid'];
//                $data['user_openid'] = $_GET['openid'];
//                $data['nickname'] = $_GET['nickname'];
//                $data['headimgurl'] = $_GET['headimgurl'];
//                //检查用户是否注册
//                check_register($data);
//            }else{
//                $data = get_wx_id();
//            }
//
//
//            $this->user_openid = $data['user_openid'];
//
//            $m = new MemberModel();
//            $status = $m->check($data['user_openid']);
//            if($status != 4){
//                $url = U('/addon/Chengjiaojia/Member/check_reg');
//                header("location:".$url);
//                exit;
//            }
            $this->user_openid = "o0nM4t4nSz5FDTJERyVctDjrylKw";
        }

        public function index(){
            echo 1;
        }
        /*
         * 获取悬赏完成状态
         * 已完成/未完成
         * */
        private function checkAgreeStatus($data){
            $m = new WantedModel();
            $result = $m->checkAgreeStatus($data);
            return $result;
        }

        /*
         * 查看悬赏购买状态
         * 已购买/未购买
         * */
        private function checkBuyStatus($data){
            $m = new WantedModel();
            $result = $m->checkBuyStatus($data);
            return $result;
        }

        //是否有人揭榜
        private function checkSendStatus($data){
            $m = new WantedModel();
            return $result = $m->checkSendStatus($data);
        }

        //判断线索状态
        private function checkKeyStatus($id){
            $m = new WantedModel();
            return $m->checkKeyStatus($id);
        }

        /*
         * 获取个人发布的悬赏列表
         * */
        public function userWantedList(){

            $user_openid = $this->user_openid;
            $result = array();
            //未验证悬赏
            $user["user_openid"] = array("eq",$user_openid);
            $status['agree_status'] = array("eq",0);
            $key = new WantedModel();
            $unBought = $key->getWantedList($status,$user);
            $this->assign("unBought",$unBought);
            //已验证悬赏
            $status['agree_status'] = array("gt",0);
            $Bought = $key->getWantedList($status,$user);

            $this->assign("Bought",$Bought);


            $this->show(CUSTOM_TEMPLATE_PATH."/Wanted/userWantedList.html");
        }

        /*
         * 检查悬赏发布者
         * */
        private function checkMember($id){
            $m = new WantedModel();
            return  $m->checkMember($id);
        }

        /*
         * 获取悬赏列表
         * 悬赏池
        */
        public function getList(){

//            $data['user_openid'] = $this->user_openid;
////            $url = "";
//            $url = U("/addon/Chengjiaojia/Member/ucenter");
////            echo U("/addon/Chengjiaojia/Member/ucenter")    ;exit;
//            $data['url'] = $url;
//            $data['first'] = "已有人购买了您的线索";
//            $data['keyword1'] = "消息提示";
//            $data['keyword2'] = date("Y-m-d H:i:s",time());
//            var_dump(sendNoticeMessage($data));exit;


//            $m = new MemberModel();
//            $member = $m->getMemberInfo($this->user_openid);
//            $data['user_openid'] = $this->user_openid;
//            $data['url'] = "{:U('/addon/Chengjiaojia/Member/Ucenter')}";
//            $data['first'] = "新积分到账";
//            $data['account'] = $member['name'];
//            $data['type'] = "线索被购买获得积分";
//            $data['creditChange'] = "到账";
//            $data['number'] = "10";
//            $data['creditName'] = "账户积分余额";
//            $data['amount'] = "100";
//            $data['remark'] = "继续加油哦！";
//
//            $result = sendKeyMessage($data);


            $m = new WantedModel();
            $data=array();
            if(isset($_GET['city']) && !empty($_GET['city'])){
                $data['city'] = $_GET['city'];
                $result = $m->getAll($data);
            }else if(isset($_GET['series']) && !empty($_GET['series'])){
                $data['series'] = $_GET['series'];
                $result = $m->getAll($data);
            }else if(isset($_GET['order']) && !empty($_GET['order'])){
                $data['order'] = $_GET['order'];
                $result = $m->getAll($data);
            }else{
                $result = $m->getAll($data);
            }
            $this->assign("wantedList",$result);
            $a = new PublicController();
            $area = $a->getArea();
            $series = $a->getSeries();
            $this->assign("area",$area);
            $this->assign("series",$series);
            $this->show(CUSTOM_TEMPLATE_PATH."/Wanted/wantedList.html");
        }

        /*
         * 获取悬赏线索详情
         * 揭榜者角度
         * */
        public function wantDetail(){
            $id = $_GET['id'];
            $m = new WantedModel();
            $result = $m->getDetail($id);
            $this->assign("data",$result);
            $this->show(CUSTOM_TEMPLATE_PATH."/Wanted/wantDetail.html");
        }

        /*
         * 获取悬赏线索详情
         * 发布者角度
         * */
        public function getWantedDetail(){
            $id = $_GET['id'];
            //判断悬赏状态
            $sendStatus = $this->checkSendStatus($id);
            $buyStatus = $this->checkBuyStatus($id);
            $agreeStatus = $this->checkAgreeStatus($id);
            //获取悬赏信息
            $wanted = new WantedModel();
            $result = $wanted ->getDetail($id);
            $this->assign("result",$result);
            $wantedKeys = array();
            $editable = "yes";
            //如果已经被人查看或者投稿的话，则不能修改
            if($sendStatus == 0){
                $this->assign("editable","yes");
            }else if($sendStatus == 1 && $buyStatus ==0){
                //已有人揭榜，仍未购买
                //查询揭榜单列表
                $wantedKeys = $wanted -> keyWantedList($id);
                $this->assign("editable","no");
                $this->assign("keys",$wantedKeys);
            }else if($buyStatus ==1 && $agreeStatus == 0){
                //已从揭榜单中购买线索，等待验证
                $this->assign("editable","no");
            }else if($agreeStatus == 1){
                //已验证完成
            }

            $this->show(CUSTOM_TEMPLATE_PATH."/Wanted/userWantedDetail.html");
        }

        /*
         * 新建悬赏
         * */
        public function createWanted(){
            $m = new MemberModel();
            $res = $m->getMemberInfo($this->user_openid);
            $this->assign("result",$res);
            $this->show(CUSTOM_TEMPLATE_PATH."/Wanted/create.html");
        }
        /*
         * 保存悬赏信息
         * */

        public function saveWanted(){
            $user_openid = $this->user_openid;
            $data = array(
                "user_openid"       => $user_openid,
                "brand"              => trim($_POST['brand']),
                "series"            => trim($_POST['series']),
                "city"            => trim($_POST['city']),
                "credit"            => trim($_POST['credit']),
                "level_limit"      => trim($_POST['level']),
//                "key_rate"          => round($_POST['key_rate']),
                "time_limit"       => $_POST['time_limit'],
                "end_time"       => date("Y-m-d H:i:s",strtotime("+ {$_POST['time_limit']} day")),
                "buy_status"        => 0,
                "send_status"       => 0,
                "agree_status"      =>0,
                "create_time"      => date("Y-m-d H:i:s",time())
            );
            $m = new MemberModel();
            $res = $m->getMemberInfo($user_openid);
            if($res['use_credit'] < $_POST['credit']){
                echo -1;    //积分不足
                exit;
            }else{
                $wanted = new WantedModel();
                $wanted_id=$wanted->save($data);
                if($wanted_id>0){
                    $credit = $_POST['credit'];
                    $m->creditChange($credit,$user_openid);
                    //发送模板消息
                    $message=array(
                        "user_openid"=>$user_openid,
                        "url" =>U('/addon/Chengjiaojia/Wanted/getWantedDetail/id/'.$wanted_id),
                        "first"=>"您发布了一条招标",
                        "keyword1" => "发布招标提示",
                        "keyword2" => date("Y-m-d H:i:s",time())
                    );
                    sendNoticeMessage($message);

                    echo 1;
                }else{
                    echo -2;
                }
            }
        }


        /*
         * 线索判断为有效
         * */

        public function useful(){
            $this->show(CUSTOM_TEMPLATE_PATH."/Wanted/useful.html");
        }

        /*
         * 线索判断为无效
         * */

        public function useless(){
            $this->show(CUSTOM_TEMPLATE_PATH."/Wanted/useful.html");
        }

        /*
         *改变线索状态
         * */
        public function changeKeyStatus(){
            //判断线索状态
            if($_GET['id']){
                $status = $this->checkStatus($_GET['id']);
            }
        }

        /*
         * 修改悬赏
         * */

        public function edit(){
            $id = $_GET['id'];
            //检查该条悬赏的状态
            $status = $this->checkStatus($id);
            if($status == 1){
                $this->error("悬赏已结束");
            }else{
                $wanted = new WantedModel();
                $result = $wanted->getDetail($id);
                $this->assign("result",$result);
                $this->show(CUSTOM_TEMPLATE_PATH."/Wanted/edit.html");
            }
        }

        public function update(){
            $id = $_GET['id'];
            $data = $_POST;
            $wanted = new WantedModel();
            $wanted->update($id,$data);
        }


        /*
         * 获取揭榜列表
         *
         * 揭榜者角度
         * */

        public function getUserWantedList(){
            $user_openid = $this->user_openid;
            $wanted = new WantedModel();
            //获取已完成的悬赏列表
            $status = 0;
            $result['unBought'] = $wanted ->getJieWantedList($status,$user_openid);
            $this->assign("unBought",$result['unBought']);
            //获取已购买揭榜线索
            $status = 1;
            $result['Bought'] = $wanted ->getJieWantedList($status,$user_openid);
            $this->assign("Bought",$result['Bought']);
//            var_dump($result);exit;
            $this->show(CUSTOM_TEMPLATE_PATH."/Wanted/getWantedList.html");
        }
        /*
         * 查询每条揭榜的悬赏中的线索列表
         * */

        public function keyWantedKeyList(){
            $id = $_GET['id'];      //悬赏编号
            $wanted = new WantedModel();
            //获取悬赏详情
            $user_openid = $this->user_openid;
            $result['info'] = $wanted->getDetail($id);
            $this->assign("info",$result['info']);
            $result['keys'] = $wanted->getKeyWantedList($id,$user_openid);
            $this->assign("keys",$result['keys']);
            $this->show(CUSTOM_TEMPLATE_PATH.'/Wanted/keyWantedList.html');
        }



//        public function keyWantedList(){
//            $id = $_GET['id'];      //线索编号
//            $wanted = new WantedModel();
//            $result = $wanted->getKeyWantedList($id);
//            $this->assign("result",$result);
//            $this->show(CUSTOM_TEMPLATE_PATH.'/Wanted/keyWantedList.html');
//        }

        /*
         * 揭榜
         * */
        public function jieBang(){
            $wanted_id = $_POST['wanted_id'];
            $key_ids = $_POST['keys'];
            $keys = explode(",",$key_ids);
            $data = array();
            $user_openid = $this->user_openid;
            $wanted = new WantedModel();

            //判断该悬赏是否已经结束
            $agreeStatus = $this->checkAgreeStatus($wanted_id);
            //判断悬赏发布者
            $wanted_openid = $this->checkMember($wanted_id);
            //查询悬赏发布者个人信息
            $member = new MemberModel();
            $wantedInfo = $member->getOne($wanted_openid);

            if($wantedInfo['user_openid'] == $user_openid){
                echo -3;
                exit;
            }


            $num = 0;

            //线索处理
            $key = new KeyModel();

            if($agreeStatus == 1){
                //如果悬赏已结束，则停止
                echo -1;
            }else{
                //如果悬赏状态正确，则添加用户揭榜记录
                foreach($keys as $k=>$v){
                    //判断线索状态
                    $status = $key->checkStatus($v);
                    if($status > 10){
                        continue;
                    }else{
                        //查询该线索是否已经揭榜
                        $rst = $wanted -> checkJieBang($wanted_id,$v);
                        if(empty($rst)){
                            $data['publisher_openid'] = $wanted_openid;     //线索发布者编号
                            $data['get_openid'] = $user_openid;              //揭榜者编号
                            $data['wanted_id'] = $wanted_id;                 //悬赏编号
                            $data['key_id'] = $v;                             //线索编号
                            $data['status'] = 0;                               //初始状态 0
                            $data['get_time'] = date("Y-m-d H:i:s",time()); //揭榜时间
                            if($wanted->saveWantedGet($data)){
                                $num++;
                                $content['is_wanted'] = 1;
                                //更新该条线索状态
                                $key->update($v,$content);
                            }else{
                                continue;
                            }
                        }else{
                            continue;
                        }

                    }
                }
                //判断悬赏状态（是否有人揭榜）
                $sendStatus = $this->checkSendStatus($wanted_id);
                if($num>0 && $sendStatus == 0){
                    $res['sendStatus'] = 1;
                    $wanted->update($wanted_id,$res);
                    //给悬赏发布者发送模板消息
                    $message=array(
                        "user_openid" => $wantedInfo['user_openid'],
                        "url" => U("/addon/Chengjiaojia/Wanted/getWantedDetail/id/".$wantedInfo['id']),
                        "first" => "您有一条招标被人投标了",
                        "keyword1"=>"招标信息提示",
                        "keyword2"=>date("Y-m-d H:i:s",time())
                    );
                    sendNoticeMessage($message);
                }
            }
            echo $num;
        }


        /*
         * 查询揭榜线索
         * */

        public function jieBangList(){
            $user_openid = $this->user_openid;
        }
//
//        public function test(){
//            $user_openid = $this->user_openid;
//            $m = new MemberModel();
//            $data['use_credit'] = -10;
//            $data['frozen_credit'] = 10;
//            $data['user_openid'] = $user_openid;
//            $m->creditChange($data);
//        }


    }

