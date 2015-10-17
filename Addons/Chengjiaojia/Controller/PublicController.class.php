<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2015/7/10
 * Time: 10:31
 */

    namespace Addons\Chengjiaojia\Controller;
    use Addons\Chengjiaojia\Controller\BaseController;
    use Addons\Chengjiaojia\Controller\SystemController;
    use Addons\Chengjiaojia\Model\MemberModel;

    class PublicController extends BaseController{
        private $user_openid;
        public function __construct(){
//            echo 111;exit;
            parent::__construct();
            $this->user_openid = session("user_openid");
        }
        public function getArea(){
            if(session("?area") && !empty(session("area"))){
                $result = session("area");
            }else{
                $sys = new SystemController();
                $result = $sys->getArea();
                session("area",$result);
            }
            return $result;
        }

        public function getSeries(){
            if(session("?series") && !empty(session("series"))){
                $result = session("series");
            }else{
                $sys = new SystemController();

                $m = new MemberModel();
                $res =  $m->getMemberInfo($this->user_openid);
                $result = $sys->getSeries();
                session("series",$result['brand']);
            }

            return $result;

        }
        //城市选择
        public function city(){
            $result = M("cj_cities")->where("is_show = 1")->select();
            $this->assign("result",$result);
            $this->show(CUSTOM_TEMPLATE_PATH."/public/city.html");
        }

        //所属县区
        public function district(){
            $city_id = $_GET['city_id'];
            $result = M("cj_district")->where("city_id = ".$city_id." and is_show = 1")->select();
            $this->assign("result",$result);
            $this->show(CUSTOM_TEMPLATE_PATH."/public/district.html");
        }


        //地址选择页面
        public function area(){
            $result = $this->getArea();
            $this->assign("result",$result);
            $this->show(CUSTOM_TEMPLATE_PATH."/public/area.html");
        }
        //品牌选择页面
        public function brand(){
            $data = array(
                "is_show" => 1,
            );
            //选择豪华品牌
            $data['good'] = 1;
            $result['good'] = M("cj_brand")->where($data)->order("listorder")->select();
            //合资品牌
            $data['good'] = 0;
            $result['normal'] = M("cj_brand")->where($data)->order("listorder")->select();
            //国产品牌
            $data['good'] = 2;
            $result['guochan'] = M("cj_brand")->where($data)->order("listorder")->select();
            $this->assign("result",$result);
            $this->show(CUSTOM_TEMPLATE_PATH."/public/brand.html");
        }
        //车系选择页面
        public function series1(){
            $result = $this->getSeries();
            $this->assign("result",$result);
            $this->show(CUSTOM_TEMPLATE_PATH."/public/series.html");
        }
        public function series(){
            $brand_id = $_GET['brand'];
            $data['brand_id'] = $brand_id;
            $data['hidden'] = 0;
            $result = M("cj_series")->where($data)->select();
            $this->assign("result",$result);
            $this->show(CUSTOM_TEMPLATE_PATH."/public/series.html");
        }
        //等级要求
        public function level(){
            $this->show(CUSTOM_TEMPLATE_PATH."/public/level.html");
        }
        //所属4S店
        public function shop(){

            $data['cityid'] = trim($_GET['cityid']);
            $data['brand_id'] = trim($_GET['brandid']);
            $data['hidden'] = 0;
            $data['status'] = 1;
            $result = M("cj_4s_shop")->where($data)->select();
            $this->assign("city_id",$data['cityid']);
            $this->assign("brand_id",$data['brand_id']);
            $this->assign("result",$result);
            $this->show(CUSTOM_TEMPLATE_PATH."/public/shop.html");
        }
        //添加所属4S店
        public function addShop(){
            $data['cityid'] = $_POST['city_id'];
            $data['brand_id'] = $_POST['brand_id'];
            $data['shop_name'] = $_POST['shop_name'];
            $data['listorder'] = 100;
            $data['hidden'] = 0;
            $data['status'] = 0;
            echo M("cj_4s_shop")->add($data);
        }



        //所属品牌
        public function shop_brand(){
            $data = array(
                "is_show" => 1,
            );
            //选择豪华品牌
            $data['good'] = 1;
            $result['good'] = M("cj_brand")->where($data)->order("listorder")->select();
            //合资品牌
            $data['good'] = 0;
            $result['normal'] = M("cj_brand")->where($data)->order("listorder")->select();
            //国产品牌
            $data['good'] = 2;
            $result['guochan'] = M("cj_brand")->where($data)->order("listorder")->select();
            $this->assign("result",$result);
            $this->show(CUSTOM_TEMPLATE_PATH."/public/shop_brand.html");
        }

        //晒单时选择所属车系
        public function shareSeries(){
            $brand_id = $_GET['brand_id'];
            $data['brand_id'] = $brand_id;
            $data['hidden'] = 0;
            $result = M("cj_series")->where($data)->select();
            $this->assign("result",$result);
            $this->show(CUSTOM_TEMPLATE_PATH."/public/shareSeries.html");
        }

        //车型页
        public function type(){
            $data['series_id'] = $_GET['series_id'];
            $result2 = M("cj_model_number")->where($data)->select();
            foreach($result2 as $key=>$value){
                $engine = $value['engine'];
                $res[$engine][$key]['name'] = $value['model_name'];
                $res[$engine][$key]['id'] = $value['id'];
            }
            $this->assign("result",$res);
            $this->show(CUSTOM_TEMPLATE_PATH."/public/type.html");
        }
    }
