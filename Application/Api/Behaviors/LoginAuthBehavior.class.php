<?php

/*
 * 用户访问控制器、方法 的权限控制
 * 
 * by luoyongyao 2016年4月14日 
 * 
 * BMT 2.0
 */

namespace Api\Behaviors;

use Think\Behavior;

class LoginAuthBehavior extends Behavior {

    public function run(&$param) {

        $auth = false; //默认访问不需要登录；

        foreach (C('LOGIN_AUTH_RULE') as $C => $t) {

            if ($C == strtolower(CONTROLLER_NAME) && $t === true) { //控制器 被限制需要登录
                $auth = TRUE;
                break;
            } elseif (is_array($t)) {

                foreach ($t as $A => $t) {
                    if ($C == strtolower(CONTROLLER_NAME) && $A == strtolower(ACTION_NAME) && $t === true) { //控制器 被限制需要登录
                        $auth = TRUE;
                        break;
                    }
                }
            }
        }

        if ($auth && empty($_SESSION['BMT_UID'])) {  //需要验证且当前用户未登录，则跳转到登录页面
  
            $data=array('return_code' => 100, 'return_msg' => '需要登录才能访问');            
            if (!empty($_GET['callback'])) {
                C('DEFAULT_AJAX_RETURN','jsonp');
                header('Content-Type:application/json; charset=utf-8');
                $handler  =   isset($_GET[C('VAR_JSONP_HANDLER')]) ? $_GET[C('VAR_JSONP_HANDLER')] : C('DEFAULT_JSONP_HANDLER');
                exit($handler.'('.json_encode($data).');');      
            }else{
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode($data));
            }     
        }
    }

}
