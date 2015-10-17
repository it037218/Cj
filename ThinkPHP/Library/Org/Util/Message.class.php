<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2015/6/28
 * Time: 16:44
 */
namespace Org\Util;
class Message
{
    public $telephone;
    public $validateNumber;
    private $parameters;

    private $username;

    private $password;

    private $client;

    function __construct()
    {
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

    /*
     * 拨打实验
     * CallValidate
     * */
    public function CallValidate(){
        $jsonArray = array (
            'guid' =>'1F58AC50687A40CA8E239BCE65DCE87C',
            'callingnum' => $this->telephone,
            'returnstr' => $this ->validateNumber,
            'exceptionInfo' => '',
        );
        $result = $this->client->__call ( "CallValidate", array(
            'parameters' =>$jsonArray
        ) );
        return $result;
    }
}