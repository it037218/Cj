<?php
namespace Addons\Chengjiaojia\Model;

use Think\Model;

class WeixinModel extends Model {
    private $appId;
    private $appSecret;

    public function __construct($appId, $appSecret) {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }





}

