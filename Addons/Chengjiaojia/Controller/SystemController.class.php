<?php

    namespace Addons\Chengjiaojia\Controller;
    use Addons\Chengjiaojia\Controller\BaseController;
    use Addons\Chengjiaojia\Model\SystemModel;
    use Addons\Chengjiaojia\Model\WeixinModel;
    use Think\Model;

    use \Org\Util;
    class SystemController extends BaseController{
        /*
         * 获取省份
         *
         * */

        private $jsapi_ticket;
        private $appid;
        private $app_secret;
        private $access_token;

        function getProvince(){
            $sql = "select p1.city,p2.province from wp_cj_cities as p1 LEFT JOIN wp_cj_provinces as p2 on p2.provinceid = p1.provinceid";

            $haha = M();
            $result = $haha->query($sql);

            $data = array();
            foreach($result as $v)
            {
                $key = $v["province"];
                $data[$key][] = $v["city"];
            }

            $m = new SystemModel();
            $result = $m->getProvince();
            echo json_encode($result);
        }

        /*
         * 获取城市
         * */
        function getCity(){
            $m = new SystemModel();
            $id = $_POST['id'];
            $result = $m -> getCities($id);
            echo json_encode($result);
        }
        /*
         * 获取所有省市
         *
         * */
        function getArea(){
            $sql = "select p1.city,p2.province from wp_cj_cities as p1 LEFT JOIN wp_cj_provinces as p2 on p2.provinceid = p1.provinceid";

            $m = M();
            $result = $m->query($sql);

            $res = array();
            foreach($result as $k=>$v)
            {
                $key = $v["province"];
                $res[$key][] = $v["city"];
            }
            return $res;
        }

        /*
         * 获取汽车品牌
         * */
        public  function getBrand(){
            $m = new SystemModel();
            $result = $m ->getCarBrand();
            echo json_encode($result);
        }

        /*
         * 获取汽车类型
         * */
        public function getSeries(){
//            $sql = "select p1.series,Concat(p2.brand,p2.factory) as brand  from wp_cj_series as p1 LEFT JOIN wp_cj_brand as p2 on p2.id = p1.brand_id";
            $sql = "select p1.series,p2.brand as brand  from wp_cj_series as p1 LEFT JOIN wp_cj_brand as p2 on p2.id = p1.brand_id";

            $m = M();
            $result = $m->query($sql);

            $res = array();
            $i = 0;
            foreach($result as $k=>$v)
            {
                $key = $v["brand"];
                $res[$key][] = $v["series"];
            }
            return $res;
        }
        //获取消息详情


        /*
         * 验证验证码正确性
         * */
        public function checkValidate(){
            $message = new Util\Message();
            $num = rand(1000,9999);
            $message ->telephone = $_GET['telephone'];
            $message ->validateNumber = $num;
            $result = $message -> CallValidate();
            session("validate",$num);
            session("telephone",$_GET['telephone']);
            echo json_encode($result);
        }

        /*
         * 微信上传图片接口
         *
         * */
        public function wxUpoloadImage()
        {
            if (isset($_POST['serverId']) && !empty($_POST['serverId'])) {

                $media_id = $_POST['serverId'];
                //获取access_token
                $access_token = $this->get_access_token();
                //根据server_id下载图片
                $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=" . $access_token . "&media_id=" . $media_id;
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_NOBODY, 0);//只取body头
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $package = curl_exec($ch);
                curl_close($ch);
                $ImgFilePath = $_SERVER['DOCUMENT_ROOT'] . "/Uploads/Avatar/";

                if (!file_exists($ImgFilePath)) {
                    mkdir($ImgFilePath, 0777, true);
                }

                $ImgFileName = $this->uuid(). ".jpg";
                $ImgFilePath .= ("/" . $ImgFileName);
                $local_file = fopen($ImgFilePath, 'w');
                if (false !== $local_file) {
                    if (false !== fwrite($local_file, $package)) {
                        fclose($local_file);
                    }
                }
                $url = "/Uploads/Avatar/" . $ImgFileName;
                echo $url;
            }
        }
        /*
         * 生成随机字符串
         * */

        function uuid($prefix = '')
        {
            $chars = md5(uniqid(mt_rand(), true));
            $uuid  = substr($chars,0,8) . '-';
            $uuid .= substr($chars,8,4) . '-';
            $uuid .= substr($chars,12,4) . '-';
            $uuid .= substr($chars,16,4) . '-';
            $uuid .= substr($chars,20,12);
            return $prefix . $uuid;
        }

        /*
         * 获取access_token
         * */
        private function  get_access_token(){

            $rst = M("cj_sys_user")->where("sys_user = 'chengjiaojia'")->find();
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $rst ['appid'] . '&secret=' . $rst ['app_secret'];
            $tempArr = json_decode ( file_get_contents ( $url ), true );
            $data['access_token'] = $tempArr['access_token'];
            $data['access_token_update_time'] = date("Y-m-d H:i:s",time());
            $data['expires_in'] = 7200;
            M("cj_sys_user")->where("sys_user = 'chengjiaojia'")->save($data);
            return $tempArr['access_token'];
        }



        /*
         * 获取getJSApiTicket
         * */
        public function getJSApiTicket(){
            $rst = M("cj_sys_user")->where("sys_user = 'chengjiaojia'")->find();
            $time = time();
            $jsapi_ticket_update_time = strtotime($rst['jsapi_ticket_update_time']);

            $expires_time = $time - $jsapi_ticket_update_time;

            if ($expires_time>7200 || $rst["jsapi_ticket"] == null || strlen($rst["jsapi_ticket"]) == 0) {
                //重新获取jsapi ticket并保存
                $ticketData = file_get_contents("https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=" . $this->get_access_token() . "&type=jsapi");
                $ticketObject = json_decode($ticketData);
                if ($ticketObject->errcode == 0){
                    $this->jsapi_ticket = $ticketObject->ticket;
                    $data['jsapi_ticket'] = $ticketObject->ticket;
                    $data['jsapi_ticket_update_time'] = date("Y-m-d H:i:s",time());
                    M("cj_sys_user")->where("sys_user = 'chengjiaojia'")->save($data);
                }
            }else{
                $this->jsapi_ticket = $rst['jsapi_ticket'];
            }
            return $this->jsapi_ticket;
        }

        /*
         * wx.js  调用jssdk时，配置需要的参数
         * */

        public function sign(){
            $url = $_POST['url'];
            $rst = M("cj_sys_user")->where("sys_user = 'chengjiaojia'")->find();

            $appid = $rst['appid'];
            $secret = $appid['app_secret'];
            $signArray = array(
                "appId" => $appid,
                "timestamp" => time(),
                "nonceStr" => $this->uuid("wx"),
                "url" =>$url
            );
            $ticks = $this->getJSApiTicket();
            //签名
            $signString =
                "jsapi_ticket=" . $ticks .
                "&noncestr=" . $signArray["nonceStr"] .
                "&timestamp=" . $signArray["timestamp"] .
                "&url=" . $signArray["url"]
            ;
            $signArray["rawString"] = $signString;
            $signArray["signature"] = sha1($signString);
//            var_dump($signArray['signature']);
//            echo "<br>";
            echo json_encode($signArray);

        }


        //接收电话消息推送
        public function getCall(){
            $str = "BigCode=4008654321&ExtCode=1000&Ani=".$_POST['Ani']."&Key=34234";
            $code = md5($str);

//            if($code == $_GET['code']){
//                $flag = 8;
//                $message = "验证失败";
//            }else{
                $flag = 0;
                $message = "验证成功";
//            }
            $result = M("cj_record_log")->add($_POST);
            $arr= array(
                "flag"=>$flag,
                "message"=>$message
            );
            echo json_encode($arr);
        }


        /*
         * 2015-10-15
         * zhuyunfeng
         * 完善用户信息
         * 调用微信接口，获取用户相关昵称，头像等信息。
         * */

        function addInfo(){
            //获取所有信息不完善的用户（缺少昵称，或者微信头像的用户）
            $result = M("cj_member")->field("user_openid,nickname,headimgurl")->select();
            foreach($result as $key=>$value){
                if($value['nickname'] == "" || $value['headimgurl'] == ""){

                    //调用微信接口，查询用户相关信息
                    $accessToken = get_access_token();
                    $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$accessToken."&openid=".$value['user_openid']."&lang=zh_CN";
                    $res1 = file_get_contents($url);
                    $res1 = json_decode($res1,true);
                    //更新用户信息
                    $con['user_openid'] = $value['user_openid'];
                    $data['headimgurl'] = $res1['headimgurl'];
                    $data['nickname'] = $res1['nickname'];
                    $res = M("cj_member")->where($con)->save($data);
                    if($res){
                        echo $value['user_openid']." success!"."<br>";
                        continue;

                    }else{
                        echo $value['user_openid']." fail!!!";
                        continue;
                    }
                }
            }
        }
        function buChongInfo(){
            //获取所有信息不完善的用户（缺少昵称，或者微信头像的用户）
            $result = M("follow")->field("openid,nickname,subscribe_time")->limit(201,300)->select();
            foreach($result as $key=>$value){
                if($value['nickname'] == ""){

                    //调用微信接口，查询用户相关信息
                    $accessToken = get_access_token();
                    $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$accessToken."&openid=".$value['openid']."&lang=zh_CN";
                    $res1 = file_get_contents($url);
                    $res1 = json_decode($res1,true);
                    //更新用户信息
                    $con['openid'] = $value['openid'];
                    $data['nickname'] = $res1['nickname'];
                    $data['create_time'] = date("Y-m-d H:i:s",$value['subscribe_time']);
                    $res = M("follow")->where($con)->save($data);
                    if($res){
                        echo $res1['nickname']." success!"."<br>";
                        continue;

                    }else{
                        echo $res1['nickname']." fail!!!"."<br>";
                        continue;
                    }
                }
            }
        }




    }
