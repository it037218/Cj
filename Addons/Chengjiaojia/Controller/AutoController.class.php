<?php

namespace Addons\Chengjiaojia\Controller;
use Addons\Chengjiaojia\Controller\BaseController;
use Addons\Chengjiaojia\Model\KeyModel;
use Addons\Chengjiaojia\Controller\PublicController;
use Addons\Chengjiaojia\Model\MemberModel;
use Addons\Chengjiaojia\Model\CreditModel;

use Think\Model;


class AutoController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        if(isset($_GET['autojudge']) && $_GET['autojudge'] == "cj8cj8cj8"){
            $this->getOverJudge();
        }else{
            exit;
        }

    }
    //检查线索状态
    private function checkStatus($id){
        $m = new KeyModel();
        return $m ->checkStatus($id);
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
    public function getKey($id){
        $data['id'] = $id;
        return M("cj_key")->where($data)->find();
    }
    public function autoJudge($key_id,$user_openid){
        //线索编号
        $id = $key_id;
        //判断结果
        $result = "true";
        //判断线索状态
        $status = $this->checkStatus($id);
        $key = new KeyModel();
        //获取线索详情
        $keyDetail = $this->getKey($id);

        //该线索积分
        $credit =   $keyDetail['credit'];

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

                //线索被判断为有效时，对线索发布者进行积分奖励
                $data['use_credit'] = 5;
                $data['frozen_credit'] = 0;
                $condition['user_openid'] = $keyDetail['user_openid'];
                changeCredit($condition,$data);

                //将积分分给线索发布者
                $data['use_credit'] = $credit;
                $data['frozen_credit'] = 0;
                $condition['user_openid'] = $keyDetail['user_openid'];
                changeCredit($condition,$data);

                //线索发布者积分增加
                $description = "您有一条线索被确认有效，增加积分";
                $credit_data = array(
                    "user_openid" => $keyDetail['user_openid'],
                    "credit" => "+10",
                    "title" => $description,
                    "description" => $description,
                    "create_time" => date("Y-m-d H:i:s",time())
                );
                addCreditLog($credit_data);

                //如果线索判断为有效，同时将购买者的积分解冻
                $data['use_credit'] = 0;
                $data['frozen_credit'] = -$credit;
                $condition['user_openid'] = $user_openid;
                changeCredit($condition,$data);

                //对购买者积分解冻扣除
                $description = "您将一条线索被确认有效，扣除积分";
                $credit_data = array(
                    "user_openid" => $keyDetail['user_openid'],
                    "credit" => "-10",
                    "title" => $description,
                    "description" => $description,
                    "create_time" => date("Y-m-d H:i:s",time())
                );
                addCreditLog($credit_data);

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
            //更新线索状态
            if($key ->changeStatus($id,$data)){
                //添加用户判断记录
                $data['key_id'] = $id;
                $data['judge_openid'] = $user_openid;
                $data['judge_level'] ="5";
                $data['judge_reason'] = "线索有效";
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
        else if($status == 12){
            //b2判断
            if($result == "true"){
                $bought_key_status = 1;
                $data['status'] =200;
                $data['b2_agree_time'] = date("Y-m-d H:i:s",time());

                //如果线索判断为有效，同时将购买者的积分解冻
                $credit =   $keyDetail['credit'];
                $data['use_credit'] = 0;
                $data['frozen_credit'] = -$credit;
                $condition['user_openid'] = $user_openid;
                changeCredit($condition,$data);
                //将积分分给线索发布者
                $data['use_credit'] = $credit;
                $data['frozen_credit'] = 0;
                $condition['user_openid'] = $keyDetail['user_openid'];
                changeCredit($condition,$data);

                //线索被判断为有效时，对线索发布者进行积分奖励
                $data['use_credit'] = 5;
                $data['frozen_credit'] = 0;
                $condition['user_openid'] = $keyDetail['user_openid'];
                changeCredit($condition,$data);

                //对线索发布者 进行经验奖励
                $condition['user_openid'] = $keyDetail['user_openid'];
                $jifen = 10;
                changeJifen($condition,$jifen);

                //线索发布者积分增加
                $description = "您有一条线索被确认有效，增加积分";
                $credit_data = array(
                    "user_openid" => $keyDetail['user_openid'],
                    "credit" => "+10",
                    "title" => $description,
                    "description" => $description,
                    "create_time" => date("Y-m-d H:i:s",time())
                );
                addCreditLog($credit_data);

                //对购买者积分解冻扣除
                $description = "您将一条线索被确认有效，扣除积分";
                $credit_data = array(
                    "user_openid" => $keyDetail['user_openid'],
                    "credit" => "-10",
                    "title" => $description,
                    "description" => $description,
                    "create_time" => date("Y-m-d H:i:s",time())
                );
                addCreditLog($credit_data);
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

                M("cj_member")->where("user_openid = '".$keyDetail['user_openid']."'")->setInc("useful_keys");
                $message = array(
                    "user_openid" => $keyDetail['user_openid'],
                    "url"   => U('/addon/Chengjiaojia/Key/publishKeyDetail/id/'.$id),
                    "first" => "您有一条线索被采纳",
                    "keyword1"=>"线索被采纳",
                    "keyword2" => date("Y-m-d H:i:s",time())
                );
                sendNoticeMessage($message);
            }

            //更新线索状态
            if($key ->changeStatus($id,$data)){
                //添加用户判断记录
                $data['key_id'] = $id;
                $data['judge_openid'] = $user_openid;
                $data['judge_level'] ="5";
                $data['judge_reason'] = "线索有效";
                $data['judge_time'] = date("Y-m-d H:i:s",time());
                $key->addJudgeLog($data);

                $key->updateBoughtKey($bought,$bought_key_status);

                echo 1;
            }
            else{
                echo -1;
            }
        }
        else if($status == 13){
            //b3判断
            if($result == "true"){
                $bought_key_status = 1;
                $data['status'] =300;
                $data['b2_agree_time'] = date("Y-m-d H:i:s",time());
                $data['judge_status'] = 1;

                //如果线索判断为有效，同时将购买者的积分解冻
                $credit =   $keyDetail['credit'];
                $data['use_credit'] = 0;
                $data['frozen_credit'] = -$credit;
                $condition['user_openid'] = $user_openid;
                changeCredit($condition,$data);

                //将积分分给线索发布者
                $data['use_credit'] = $credit;
                $data['frozen_credit'] = 0;
                $condition['user_openid'] = $keyDetail['user_openid'];
                changeCredit($condition,$data);

                //线索被判断为有效时，对线索发布者进行积分奖励
                $data['use_credit'] = 5;
                $data['frozen_credit'] = 0;
                $condition['user_openid'] = $keyDetail['user_openid'];
                changeCredit($condition,$data);

                //对线索发布者 进行经验奖励
                $condition['user_openid'] = $keyDetail['user_openid'];
                $jifen = 10;
                changeJifen($condition,$jifen);

                //线索发布者积分增加
                $description = "您有一条线索被确认有效，增加积分";
                $credit_data = array(
                    "user_openid" => $keyDetail['user_openid'],
                    "credit" => "+10",
                    "title" => $description,
                    "description" => $description,
                    "create_time" => date("Y-m-d H:i:s",time())
                );
                addCreditLog($credit_data);

                //对购买者积分解冻扣除
                $description = "您将一条线索被确认有效，扣除积分";
                $credit_data = array(
                    "user_openid" => $keyDetail['user_openid'],
                    "credit" => "-10",
                    "title" => $description,
                    "description" => $description,
                    "create_time" => date("Y-m-d H:i:s",time())
                );
                addCreditLog($credit_data);
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

                //线索发布者被评为有效的线索条数+1
                M("cj_member")->where("user_openid = '".$keyDetail['user_openid']."'")->setInc("useful_keys");

                //给线索发布者发送模板消息
                $message = array(
                    "user_openid" => $keyDetail['user_openid'],
                    "url"   => U('/addon/Chengjiaojia/Key/publishKeyDetail/id/'.$id),
                    "first" => "您有一条线索被采纳",
                    "keyword1"=>"线索被采纳",
                    "keyword2" => date("Y-m-d H:i:s",time())
                );
                sendNoticeMessage($message);
            }

            //更新线索状态
            if($key ->changeStatus($id,$data)){
                //添加用户判断记录
                $data['key_id'] = $id;
                $data['judge_openid'] = $user_openid;
                $data['judge_level'] ="5";
                $data['judge_reason'] = "线索有效";
                $data['judge_time'] = date("Y-m-d H:i:s",time());
                $key->addJudgeLog($data);

                $key->updateBoughtKey($bought,$bought_key_status);

                echo 1;
            }
            else{
                echo -1;
            }
        }
        else{
            echo -2;
        }
    }
}