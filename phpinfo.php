<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 15/10/19
 * Time: 上午10:02
 */

    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    echo "Connection to server sucessfully";
    //查看服务是否运行
//    echo "Server is running: ". $redis->ping();




    $redis->lpush("tutorial-list", "Redis");
    $redis->lpush("tutorial-list", "Mongodb");
    $redis->lpush("tutorial-list", "Mysql");
    // 获取存储的数据并输出
    $arList = $redis->lrange("tutorial-list", 0 ,5);
//    echo "Stored string in redis:: ";
//       print_r($arList);


    $news = array(
        "user_openid"=>"21312412",
        "message"=>"hahhahahahahhaa",
        "create_time"=>"2015-10-20 11:11;11"
    );
    $redis->lPush("news",$news);
    $result = $redis->lRange("news",0,10);
    var_dump($result);