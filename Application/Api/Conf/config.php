<?php

return array(
    'URL_ROUTER_ON' => true, //开启路由
    'URL_ROUTE_RULES' => array(
        'login' => 'users/login',
        'restpwd' => 'users/restpwd',
        'reg' => 'users/reg',
        'regStore' => 'users/regStore',
        'getcode' => 'users/getcode',
        'checkcode' => 'users/checkcode',
        'user/address' => 'users/address',
        'cat/list' => 'item/cats',
        'wxinfo'=>'users/wxinfo',
    ),
 
    'SESSION_OPTIONS'=>['domain'=>'.baobeimt.cn','expire'=>864000],
    /**
     * 会员访问控制器和方法名 权限控制规则；
     *
     * 控制器 =true  表示这个控制器需要登录后才能访问
     *
     * 控制器.方法 = true  表示这个方法需要登录后才能访问
     *
     * by luoyongyao  2016年4月14日
     *
     * BMT 2.0
     *
     */
    'LOGIN_AUTH_RULE' => [

        'item' => [
            'quantity' => TRUE,
            'cats' => TRUE,
            'item_list' => TRUE,
            'detail' => TRUE,
        ],
        'trade' => [
            'cancel' => true,
            'success' => TRUE,
            'trade_list' => TRUE,
            'trace_detail' => TRUE,
            'detail' => TRUE,
        ],
        'pay' => ['index'=>true],
        'order' => [
            'submit' => TRUE,
            'item' => TRUE,
        ],
        'user' => [
            'address' => TRUE
        ],
        'bd' => TRUE,
        'activity'=>TRUE

    ],
    //微信日志是否开启
    'WECHAT_DEBUG_LOG' => true,

    //登录url
//    'LOGIN_URL' => '/bbmt/',
//    //注册url
//    'REGISTER_URL' => '/bbmt/template/Login/register.html',
    
    
    'route_url'=>[
        
        100=>'http://wx2.baobeimt.cn/bbmt/',  //登陆页面
        
        1=>'http://wx2.baobeimt.cn/bbmt/template/cats/cats.html', //全部商品
        
        2=>'http://wx2.baobeimt.cn/bbmt/template/personal-store/mine.html',//BD
        
        0=>'http://wx2.baobeimt.cn/bbmt/template/Login/register.html',//注册
        
        3=>'http://wx2.baobeimt.cn/bbmt/template/order/order_list.html',//所有订单
        
    ]
    
);