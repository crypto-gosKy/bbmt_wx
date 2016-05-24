<?php
return array(
    'DEFAULT_MODULE'        =>  'Api',  // 默认模块
    'FILE_UPLOAD_TYPE' => 'Aliyun',
    'UPLOAD_TYPE_CONFIG' => array(
        'AccessKeyId' => '29IF6n1MZ3DhHyO2',  
        'AccessKeySecret' => 'UeqU6SSweYxZt5tetZcdYfBkVhvXon',  
        'domain' => 'oss-cn-hangzhou.aliyuncs.com',  
        'bucket' => 'bmtsrc', 
    ),

    'MEMCACHED_SERVER'=>'ac5bc48bfaa749a2.m.cnhzaliqshpub001.ocs.aliyuncs.com',
    'MEMCACHED_PORT'=>'11211',
//    'MEMCACHED_USER'=>'',
//    'MEMCACHED_PWD'=>''

    'LANG_SWITCH_ON' => true,        //开启多语言支持开关
    'DEFAULT_LANG' => 'zh-cn',    // 默认语言中文
    'LANG_LIST' => 'zh-cn,en-us', // 允许切换的语言列表 用逗号分隔
    'LANG_AUTO_DETECT' => true,      //自动检测语言区域
    'VAR_LANGUAGE' => '1', // 默认语言切换变量
     
    //加载其他配置
    'LOAD_EXT_CONFIG' => array(
        'config_db',
        'config_debug',
        'config_cache',
        'config_wxpay',//公众号配置
        'config_express'//快递公司编码
    ),
    //阿里大鱼短信验证码测试
    'AlidayuAppKey'=>'23315570',//阿里大鱼appkey
    'AlidayuAppSecret'=>'5833f28bb751b484d014b2946697e1f9',//阿里大鱼secret
    'AlidayuApiEnv'    => 1, // api请求地址，1为正式环境，0为沙箱环境

     //阿里大鱼短信模板定义数组 可通过修改配置文件的方式实现短信模板的增加修改等
     //短信验证码模板
    
    'SMS_TPLS'=>array(
        'reg'=>['id'=>'SMS_5074660','vars'=>['product'=>'用户注册']],//注册
        'restpwd'=>['id'=>'SMS_5074655','vars'=>['product'=>'找回密码']]//重设密码
    ),
     'MsgSign'=>'大鱼测试',

     /* 自定义配置 */
    /*默认ajax返回*/
    'DEFAULT_AJAX_RETURN'=>'json',
    
    'TMPL_ENGINE_TYPE'		=> 'php',
    
    'SESSION_TYPE' => 'Db',//SESSION 引擎 

);