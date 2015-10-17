<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2015/6/28
 * Time: 10:16
 */

namespace Addons\Chengjiaojia\Controller;
use Addons\Chengjiaojia\Controller\BaseController;
use Addons\Chengjiaojia\Model\MessageModel;
use Think\Model;

class MessageController extends BaseController {


    private function checkStatus($id){
        $m = new MessageModel();
        $result = $m->checkStatus($id);
        return $result;
    }
    //查看消息
    public function readMessage(){
        $id = $_GET['id'];
        //检查消息状态  0：未读；1：已读
        $result = $this->checkStatus($id);
        $m = new MessageModel();
        if($result['status'] == 0){
            $m->changeStatus($id);
            $message = $m->getMessageDetail($id);
            $this->assign("result",$message);
            $this->show(CUSTOM_TEMPLATE_PATH."/Message/messageDetail");
        }else{
            $message = $m->getMessageDetail($id);
            $this->assign("result",$message);
            $this->show(CUSTOM_TEMPLATE_PATH."/Message/messageDetail");
        }
    }
    //改变消息装填
    public function changeStatus(){
        $id = $_GET['id'];
        $data['status'] = 1;
        return M("cj_message")->where("id = ".$id)->save($data);
    }


}