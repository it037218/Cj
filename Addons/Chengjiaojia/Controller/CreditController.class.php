<?php

    /*
     * 赚积分
     * 用户通过投放有效线索，赚取积分
     * */

    namespace Addons\Chengjiaojia\Controller;
    use Addons\Chengjiaojia\Controller\BaseController;
    use Addons\Chengjiaojia\Model\CreditModel;
    use Addons\Chengjiaojia\Model\MemberModel;

    class CreditController extends BaseController{

        //积分操作页面
        public function index(){
            $m = new CreditModel();

            $this->show(CUSTOM_TEMPLATE_PATH."/jifen/jifen.html");
        }

        /*
         * 冻结积分
         * */

        public function freezeCredit($data){
            $data = array(
                "user_openid"=>$data['user_openid'],
                "credit"    =>$data['credit'],
                "description"=>$data['description'],
                "create_time" => date("Y-m-d H:i:s",time()),
            );

            $obj = new CreditModel();
            $result = $obj -> addCreditLog($data);
        }


        /*
         * 获取用户的积分日志
         * */
        public function getCreditList(){
            $user_openid = session("user_openid");
            $credit = new CreditModel();
            $result = $credit->getCreditList();

        }



    }
?>