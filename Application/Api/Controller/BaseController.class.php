<?php

/**
 * by luoyongyao 2016年5月5日 
 */

namespace Api\Controller;

use Think\Controller;

class BaseController extends Controller {

    public function __construct() {
        parent::__construct();

        if (!empty($_GET['callback'])) {
             C('DEFAULT_AJAX_RETURN','jsonp');
        }
        
        /**
         * 初始化 BMT 全局变量；
         * by luoyongyao 2016年4月20日 
         */
        if (empty($_SESSION['BMT_UID'])) {
            $_SESSION['BMT_UID'] = 0;
            $_SESSION['BMT_UNAME'] = !empty($_SESSION['BMT_UNAME'])?$_SESSION['BMT_UNAME']:"";
            $_SESSION['BMT_OPENID'] = !empty($_SESSION['BMT_OPENID'])?$_SESSION['BMT_OPENID']:"";
            $_SESSION['BMT_BDUSER'] = '';
        }
    }
    
    public function index() {
        
         exit('Welcome to BMT API server');
        
    }
    public function _empty(){
 
        header('HTTP/1.1 404 Not Found');
        exit('404');
    }


    /**
     * 向终端 输出json 字符串；
     * 
     * $result 不等于 false 时 输出   {'return_code':0, 'data':'数据内容'];
     * 
     * $result 等于  false 时 {'return_code':1, 'return_msg':'字符串内容'];
     * 
     *    $err 是 model对象时 输出 {'return_code':1, 'return_msg':'model error 内容'];  model error 只取一条
     *    $err 是字符串内容时 输出 {'return_code':1, 'return_msg':'字符串内容'];
     *    $err 是数组内容时 输出 {'return_code':1, 'return_msg':'一条数组值'];
     * 
     * @param mix $result
     * @param mix $err
     */
    public function echoJSON($result,$err=null) {

        if ($result === false) {

            if(is_object($err) && method_exists($err, 'getError')){
              $err= $err->getError(); //如果是error是数组的话，返回第一条 error；
            }
            if(empty($err)){  $err='未知错误';  };
            parent::ajaxReturn(['return_code' => 1, 'return_msg' =>is_array($err)?array_pop($err):$err ] );
        } else {
            parent::ajaxReturn(['return_code' => 0, 'data' => $result]);
        }
    }

}
