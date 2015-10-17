<?php

    namespace Org\Util;


class Record{
    public $client;
    public $username;
    public $password;

    public function __construct(){

        // 定义本页的编码
        header ( "content-type:text/html; charset=UTF-8" );

        // 设置webservice地址和命名空间,使用此方法调用webservice，需要使用php5以上版本，然后加载php_soap.dll
        //以下内容固定，请设置成系统参数，可以进行调整。
        @define ( 'SoapBaseUrl', "http://60.247.29.36/interface/apiservice.asmx" );
        @define ( 'SoapBaseNameSpace', "http://tempuri.org/" );

        $this->client = new \SoapClient ( SoapBaseUrl . "?wsdl" ); // 初始化soap
        $this->client->soap_defencoding = 'UTF-8'; // 定义编码
        $this->client->decode_utf8 = false; // 定义编码
        $this->client->xml_encoding = 'utf-8'; // 定义编码


        // 定义soap头信息,密码请采用编码后字串
        $this->username = 'chengjiao'; // 用户名
        $this->password = 'PKdeo93k2j4Awf'; // 密码
        $auth = array (
            'LoginName' => $this->username,
            'Pwd' => $this->password,
            'RoleID' => 3
        );
        $authvalues = new \SoapVar ( $auth, SOAP_ENC_OBJECT, 'Header', SoapBaseNameSpace );

        $header = new \SoapHeader ( SoapBaseNameSpace, "Header", $authvalues, true );
        $this->client->__setSoapHeaders ( array (
            $header
        ) );
    }

    //查看电话明细
    public function GetAgentMoney(){

        // 以数组的形式赋值要调用的方法参数

        $parameters = array (
            'MoneyType' => 1,
            'exceptionInfo' => ''
        );

        // 调用方法
        $result = $this->client->__call ( "GetAgentMoney", array (
            'parameters' => $parameters
        ) );
        echo "<br>" . $result->GetAgentMoneyResult;
        echo "<br>" . $result->exceptionInfo;
        echo "<br>";
        var_dump ( $result );

        echo "<br><br>";


    }

    //拨打电话
    public function Call($a,$b){
        // 拨打实验
        // ===========================================================
        //准备拨打的电话号码
        $jsonArray = array (
            'bigcode' => '4008654321',//系统设置的400号码（请云峰参数化配置）
            'extcode' => '1000',//系统设置的分机号（请云峰参数化配置）
            'direction' => 1, // 1 先呼客户，0 先呼坐席，可以更改呼叫先后顺序
            'custnum' => $a , // 客户电话号码
            'callingnum' => $b, // 呼叫号码
            'returnstr' => '',
            'crmuserid' => '',
            'crmusername' => '',
            'userloginname' => ''
        );

        $param = array (
            'sJsonValues' => json_encode ( $jsonArray )
        );

        $result = $this->client->__call ( "CallBack_s", array (
            'parameters' => $param
        ) );
        return $result;

    }


    public function GetFreeDayDetail($time){

        $jsonArray = array (
            'dt' => date("Y-m-d"),
            'supplierID' => 0,
            'exceptionInfo' => ''
        );

        $result = $this->client->__call ( "GetFreeDayDetail", array (
                'parameters' => $jsonArray
            )
        );
        var_dump($result);exit;

    }




}
?>