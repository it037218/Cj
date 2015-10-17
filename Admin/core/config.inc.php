<?php
//数据库配置信息
//define('DB_HOST', '222.73.218.92'); //数据库服务器主机地址
//define('DB_USER', 'root'); //数据库帐号
//define('DB_PW', 'YpMysql@2013*IK<'); //数据库密码
//define('DB_NAME', 'weixin'); //数据库名

define('DB_HOST', '10.66.102.150'); //数据库服务器主机地址
define('DB_USER', 'root'); //数据库帐号
define('DB_PW', 'Aoe0HJewk3920'); //数据库密码
define('DB_NAME', 'chengjiao'); //数据库名

//数据库服务器主机地址
//define('DB_HOST', '127.0.0.1'); //数据库服务器主机地址
//define('DB_USER', 'root'); //数据库帐号
//define('DB_PW', '62487e1f'); //数据库密码
//define('DB_NAME', 'cj-test'); //数据库名

//define('DB_HOST', '192.168.1.166'); //数据库服务器主机地址
//define('DB_USER', 'youpeng'); //数据库帐号
//define('DB_PW', 'Youpeng@2013'); //数据库密码
//define('DB_NAME', 'wxtest'); //数据库名

//define('DB_PRE', 'phpcms_'); //数据库表前缀，同一数据库安装多套Phpcms时，请修改表前缀
define('DB_CHARSET', 'utf8'); //数据库字符集
define('DB_PCONNECT', 1); //0 或1，是否使用持久连接,1-表示持久连接

define('DB_LOG',0);//开启日志
define('DB_LOG_FILEPATH','../data/');//开启日志

//define('DB_DATABASE', 'mysql'); //数据库类型
//网站路径配置
define('PHPCMS_PATH', '/'); //Phpcms框架访问路径，相对于域名

//数据存文本目录
//define('CONTENT_ROOT', PHPCMS_ROOT.'data/txt/'); //默认存储路径
define('CONTENT_ROOT', '/data/txt/'); //默认存储路径

//缓存配置
//define('CACHE_STORAGE', 'files'); //Cache 存储方式（files, mysql, apc, eaccelerator, memcache, shmop）
//define('CACHE_PATH', PHPCMS_ROOT.'data/cache/'); //缓存默认存储路径
//define('CACHE_MODEL_PATH', PHPCMS_ROOT.'data/cache_model/'); //模型缓存存储路径

//页面缓存配置
//define('CACHE_PAGE', 0); //是否开启PHP页面自动缓存功能
//define('CACHE_PAGE_PATH', PHPCMS_ROOT.'data/cache_page/'); //缓存存储路径
//define('CACHE_PAGE_TTL', 3600); //秒，缓存默认生命周期
//define('CACHE_PAGE_INDEX_TTL', 300); //秒，缓存默认生命周期
//define('CACHE_PAGE_CATEGORY_TTL', 600); //秒，缓存默认生命周期
//define('CACHE_PAGE_LIST_TTL', 900); //秒，缓存默认生命周期
//define('CACHE_PAGE_CONTENT_TTL', 14400); //秒，缓存默认生命周期

//Session配置
define('SESSION_STORAGE', 'mysql'); //Session 存储方式（files, mysql, apc, eaccelerator, memcache, shmop）
define('SESSION_TTL', 1800); //Session 生命周期（秒）
//define('SESSION_SAVEPATH', PHPCMS_ROOT.'/data/sessions/'); //Session 保存路径（files）
define('SESSION_SAVEPATH', '/data/sessions/'); //Session 保存路径（files）
define('SESSION_N', 0); //Session 文件分布的目录深度（files）

//Cookie配置
define('COOKIE_DOMAIN', ''); //Cookie 作用域
define('COOKIE_PATH', '/'); //Cookie 作用路径
define('COOKIE_PRE', ''); //Cookie 前缀，同一域名下安装多套Phpcms时，请修改Cookie前缀
define('COOKIE_TTL', 0); //Cookie 生命周期，0 表示随浏览器进程

//模板相关配置
//define('TPL_ROOT', PHPCMS_ROOT.'templates/'); //模板保存物理路径
//define('TPL_NAME', 'default'); //当前模板方案目录
//define('TPL_CSS', 'default'); //当前样式目录
//define('TPL_CACHEPATH', PHPCMS_ROOT.'data/cache_template/'); //模板缓存物理路径
//define('TPL_REFRESH', 1); //是否开启模板缓存自动刷新

//附件相关配置
//define('UPLOAD_FRONT', 1); //是否允许前台上传附件
//define('UPLOAD_ROOT', PHPCMS_ROOT.'uploadfile/'); //附件保存物理路径
//define('UPLOAD_URL', 'uploadfile/'); //附件目录访问路径
//define('UPLOAD_ALLOWEXT', 'doc|docx|xls|ppt|wps|zip|rar|txt|jpg|jpeg|gif|bmp|swf|png'); //允许上传的文件后缀，多个后缀用“|”分隔
//define('UPLOAD_MAXSIZE', 1024000); //允许上传的附件最大值
//define('UPLOAD_MAXUPLOADS', 100); //前台同一IP 24小时内允许上传附件的最大个数
//define('UPLOAD_FUNC', 'copy'); //文件上传函数（copy, move_uploaded_file）

//Ftp相关配置
define('FTP_ENABLE', 0); //Ftp主机
define('FTP_HOST', '127.0.0.1'); //Ftp主机
define('FTP_PORT', '21'); //Ftp端口
define('FTP_USER', ''); //Ftp帐号
define('FTP_PW', ''); //Ftp密码
define('FTP_PATH', '/'); //Ftp默认路径

define('CHARSET', 'utf-8'); //网站字符集
define('TIMEZONE', 'Etc/GMT-8'); //网站时区（只对php 5.1以上版本有效），Etc/GMT-8 实际表示的是 GMT+8
define('DEBUG', 1); //是否显示调试信息
define('ADMIN_LOG', 0); //是否记录后台操作日志
define('ERRORLOG', 0); //是否保存错误日志
define('FILTER_ENABLE', 1); //非法信息屏蔽作用范围（0 禁用，1 前台，2 全站）
define('GZIP', 0); //是否Gzip压缩后输出
define('AUTH_KEY', ''); //Cookie密钥
define('PASSWORD_KEY', ''); //会员密码密钥，为了加强密码强度防止暴力破解，不可更改
define('MEMBER_FIELDS', 'username,password,groupid,email,areaid,amount,point,modelid,message'); //默认读取的会员全局变量字段
define('ALLOWED_HTMLTAGS', '<a><p><br><hr><h1><h2><h3><h4><h5><h6><font><u><i><b><strong><div><span><ol><ul><li><img><table><tr><td><map>'); //前台发布信息允许的HTML标签，可防止XSS跨站攻击
define('LANG', 'zh-cn'); //网站语言包

define('ADMIN_FOUNDERS', '1'); //网站创始人ID，多个ID逗号分隔

//安全相关配置
define('FILE_MANAGER', '0'); // 是否允许使用文件管理器 1=是 0=否[安全]
define('ACTION_TEMPLATE', '1'); //是否允许使用模板修改 1=是 0=否[安全]
define('EXECUTION_SQL', '0'); //是否允许执行SQL        1=是 0=否[安全]

//上传图片相关配置信息
//$upload_obj->FileType = array('image/gif','image/jpg','image/jpeg', 'image/png','image/bmp'); // 允许上传的文件类型
//$upload_obj->FileMaxSize = array('image' => 100 * 1024);
//$upload_obj->FileSavePath = array('upload/files/s/', 'upload/files/z/');
//$upload_obj->FileSaveName = time();
//$upload_obj->NoteFileFalse = '文件错误';
//$upload_obj->NoteFileType  = '文件类型不符';
//$upload_obj->NoteFileSize  = '文件大小超出';

$IMG_FILE_TYPE =array('image/gif','image/jpg','image/jpeg', 'image/png','image/bmp'); // 允许上传的文件类型
$IMG_FILEMAXSIZE = array('image' => 1000 * 1024);
$IMG_FILESAVEPATH = '../upload/images/';
$IMG_FILESAVENAME = time(). rand( 1 , 10000 );
$IMG_NOTEFILEFALSE = '文件错误';
$IMG_NOTEFILETYPE = '文件类型不符';
$IMG_NOTEFILESIZE = '文件大小超出';
define("UPLOAD_IMAGE_SIZE", 1024000);
define("UPLOAD_IMAGE_SIZE_KB", 1000); 
define('IMG_FILE_SAVEPATH','../upload/images/');
define('HTML_FILE_SAVEPATH','../upload/html/');
define('REPORT_FILE_SAVEPATH','../report/');
define("QRCODE_IMAGE_SAVEPATH", "../qrcode/");

define('HTML_HEAD','<!DOCTYPE html><html><head><meta content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name="format-detection" content="telephone=no" /><meta name="apple-mobile-web-app-capable" content="yes"/><link rel="stylesheet" type="text/css" href="../../../css/cust.css"/></head><body id="activity-detail"><div class="page-content">');
define('HTML_END','</div></body></html>');
//define('WEB_SITE','http://localhost/wx');
define('WEB_SITE','http://wx.youpengchina.com');
define('API_URL','http://wx.youpengchina.com/api/wx.php');
define('MENU_API','http://wx.youpengchina.com/api/cmenu.php?publicId=');

define('HTML_HEAD1','<!DOCTYPE html><html><head><meta content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name="format-detection" content="telephone=no" /><meta name="apple-mobile-web-app-capable" content="yes"/><link rel="stylesheet" type="text/css" href="../../../css/cust.css"/></head>');
define('HTML_TITLE_BEFORE','<title>');
define('HTML_TITLE_AFTER','</title>');
define('HTML_BODY','<body id="activity-detail"><div class="page-content">');
define('HTML_END1','</div></body></html>');
define('VOICE_FILE_SAVEPATH','../upload/voice/');
$VOICE_FILE_TYPE=array('mp3','wma','wav','amr');
define('VOICE_FILE_MAXSIZE',5000000);//最大5M
define('Report_FILE_MAXSIZE',2000000);//最大2M
define('WHEEL_NAME','幸运大转盘');//表示幸运大转盘
define('SCRATCH_NAME','刮刮卡');//表示刮刮卡
define('COUPON_NAME','优惠券');//表示优惠券

define("MALL_COMPANY_WEIXIN_ID", "youpengchina");

define('weixin_domain_getheadimg', 'https://mp.weixin.qq.com/cgi-bin/getheadimg');
define('weixin_domain_getimgdata', 'https://mp.weixin.qq.com/cgi-bin/getimgdata');
define('weixin_image_save_path', '/wxupload/image');
define('weixin_image_url', 'http://wx.youpengchina.com/wxupload/image');

?>
