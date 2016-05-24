<?php
 
/**
 * 
 * 微信支付回调 应用入口文件
 * 
 * by luoyongyao 2016年4月22日  
 * 
 */

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

 


 
// 定义应用目录
define('APP_PATH','./Application/');

define('BIND_MODULE', 'Api');

define('BIND_CONTROLLER', 'Pay');

define('BIND_ACTION', 'notify');


$inputdata = file_get_contents("php://input");
 

$postdata = json_decode(json_encode(simplexml_load_string($inputdata, 'SimpleXMLElement', LIBXML_NOCDATA)), true);


// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';

