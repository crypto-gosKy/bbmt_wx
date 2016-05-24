<?php
/**
 *  微信支付配置
 */
return [
   
    'WxPayConf'=> [
        'wxpay_appid' => 'wxa181a00befa528ff',
        //受理商ID，身份标识
        'wxpay_mchid' => '1316190501',
        //商户支付密钥Key。审核通过后，在微信发送的邮件中查看
        'wxpay_key' => 'IZh411x4lUO1rSysThnkJ0XGYur2YUDa',
        //JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
        'wxpay_appsecret' => 'da0594b7526cb395ec80a7b1738db660',
        //=======【JSAPI路径设置】===================================
        'CURL_TIMEOUT' => 30,
        
        'notify_url'=>'http://wx2.baobeimt.cn/wxpaycallback.php',//微信回调地址；
    ]
];
