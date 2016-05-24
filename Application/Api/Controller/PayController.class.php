<?php

/**
 * ------------------------------------------------
 *  微信支付 
 * ----------------------------------------------------------------------------
 *  
 * ----------------------------------------------------------------------------
 */
namespace Api\Controller;

use Think\Controller;

/**
 * 微信支付类
 */
class payController  extends Controller
{

    protected $parameters;
    protected $payment;


    public function __construct() {
        parent::__construct();
         
        $this->payment=C('WxPayConf');
        
    }
    
    
    /**
     * 返回微信付款 
   $jsApiParameters 
     */
   public function index()
    {
 
        $tid = I('tid');
        
        if ($tid<1 ) {
            
            $this->ajaxReturn(['return_code'=>1,'return_msg'=>'tid不能为空']);
        }
        
        $trade = M('Trades')->where(['tid'=>$tid,'buyer_user_id'=> intval($_SESSION['BMT_UID'])])->find();
        
         if (empty($trade)   ) {
            
            $this->ajaxReturn(['return_code'=>1,'return_msg'=>'付款交易不存在']);
        }
         
         if ($trade['status']!=WAIT_BUYER_PAY  ) {  //必须是待付款的交易才能支付
            
            $this->ajaxReturn(['return_code'=>1,'return_msg'=>'当前交易不允许付款']);
        }
         
        
        if (empty($_SESSION['BMT_OPENID'])) {
             
            $openid=M('wechat_user')->where(['bmt_uid'=>intval($_SESSION['BMT_UID'])])->getField('openid');
            
        }else{
            
            $openid=$_SESSION['BMT_OPENID'];
        }
        
        //测试用户
        
        if(isset(C('testuser')[$_SESSION['BMT_UID']])){ $openid = C('testuser')[$_SESSION['BMT_UID']]; };
         
        if (empty($openid)) {
            
             $this->ajaxReturn(['return_code'=>1,'return_msg'=>'openid不正确，无法完成微信支付']);
        }
 
        // 设置必填参数
        // 根目录url
        $this->setParameter("openid", "$openid"); // 商品描述
        $this->setParameter("body", mb_substr($trade['title'], 0,60,'utf-8')); // 商品描述
        $this->setParameter("out_trade_no", $trade['tid'].'-'.$_SESSION['BMT_UID']  ); // 商户订单号
        $this->setParameter("total_fee", $trade['pay_amount']  ); // 总金额  分 
       // $this->setParameter("notify_url",U('Api/Pay/notify','',TRUE,TRUE)); // 通知地址
        
        $this->setParameter("notify_url", $this->payment['notify_url']); // 通知地址
        
        $this->setParameter("trade_type", "JSAPI"); // 交易类型
        
        $this->setParameter("attach", "user_id:".$_SESSION['BMT_UID']);// 附加数据  
         
        $prepay_id = $this->getPrepayId();
        
        $jsApiParameters = $this->getParameters($prepay_id);
                
        // wxjsbridge
//        $html = '<script language="javascript">
//        function jsApiCall(){WeixinJSBridge.invoke("getBrandWCPayRequest",' . $jsApiParameters . ',function(res){if(res.err_msg == "get_brand_wcpay_request:ok"){  show_msg("pay_success") }else{  show_msg("pay_error") }});}function callpay(){if (typeof WeixinJSBridge == "undefined"){if( document.addEventListener ){document.addEventListener("WeixinJSBridgeReady", jsApiCall, false);}else if (document.attachEvent){document.attachEvent("WeixinJSBridgeReady", jsApiCall);document.attachEvent("onWeixinJSBridgeReady", jsApiCall);}}else{jsApiCall();}}
//            </script>';
//        $html.= '<div style="text-align:center"><button class="btn-info ect-btn-info" style="background-color:#44b549;" type="button" onclick="callpay()">微信付款</button></div>';
        
         $this->ajaxReturn(['return_code'=>0,'data'=>$jsApiParameters ]);
    }

  
    /**
     * 响应操作
     */
   public function notify()
    {
       
       global  $postdata;
       
       // $inputdata = $GLOBALS['$postdata'];
        if (APP_DEBUG) {
            trace($postdata, 'wxpaynotify', 'DEBUG',true);
        }
        if (!empty($postdata)) {
     
         //   $postdata = json_decode(json_encode(simplexml_load_string($inputdata, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
            /* 检查插件文件是否存在，如果存在则验证支付是否成功，否则则返回失败信息 */
            // 微信端签名
            $wxsign = $postdata['sign'];
            unset($postdata['sign']);

            // 微信附加参数
            $attach = $postdata['attach'];

            foreach ($postdata as $k => $v) {
                $Parameters[$k] = $v;
            }
            // 签名步骤一：按字典序排序参数
            ksort($Parameters);

            $buff = "";
            foreach ($Parameters as $k => $v) {
                $buff .= $k . "=" . $v . "&";
            }
            $String;
            if (strlen($buff) > 0) {
                $String = substr($buff, 0, strlen($buff) - 1);
            }
            // 签名步骤二：在string后加入KEY
            $String = $String . "&key=" . $this->payment['wxpay_key'];
            // 签名步骤三：MD5加密
            $String = md5($String);
            // 签名步骤四：所有字符转为大写
            $sign = strtoupper($String);
            // 验证成功
            if ($wxsign == $sign) {
                // 交易成功
                if ($postdata['result_code'] == 'SUCCESS') {
                     
                    list($tid,$user_id) = explode('-', $postdata['out_trade_no']);
                     
                    $Trades= M('Trades')->where(['tid'=>$tid])->find();
                  
                    if ($Trades   &&  $Trades['buyer_user_id']==$user_id  && $Trades['pay_amount']==$postdata['total_fee']  ) {
                        
                        $st=null;
                        
                        if($Trades['trade_type']==TRADE_TYPE_CN){
                            
                            $st=WAIT_SELLER_SEND_GOODS;//国内商品交易
                            
                        }elseif($Trades['trade_type']==TRADE_TYPE_FTZ){
                            
                            $st=WAIT_CUSTOM_CLEARANCE;//保税区商品交易
                            
                        }else{  //如果无法确定商品交易类型  只能支付失败；
                            
                            $returndata['return_code'] = 'FAIL';
                            $returndata['return_msg'] = '无法确定商品交易类型,TID:'.$tid;
                        }
                        
                        if ($st!==null) {
                            
                            D('Trades')->startTrans();
                            //变更交易的 状态
                            $t= D('Trades')->where(['tid'=>$tid])->save(['status'=>$st,'pay_time'=>time()]);
                             //变更订单状态
                            $t2=D('TradeOrders')->where(['tid'=>$tid])->save(['status'=>$st]);
                            
                            if ($t && $t2) {
                                
                                D('Trades')->commit();
                                
                            }else{
                                
                                D('Trades')->rollback();
                            }
                            
                             
                            $returndata['return_code'] = 'SUCCESS';
                            
                        }
                        
                        
                    }  else {
                        
                            $returndata['return_code'] = 'FAIL';
                            $returndata['return_msg'] = '无权限处理这个交易,TID:'.$tid;
                    }
                    
                    
//                    if(method_exists('WechatController', 'do_oauth')){
//                        /* 如果需要，微信通知 wanglu */
//                        $order_id = model('Base')->model->table('order_info')
//                            ->field('order_id')
//                            ->where('order_sn = "' . $order_trade_no[0] . '"')
//                            ->getOne();
//                        $order_url = __HOST__ . url('user/order_detail', array(
//                                'order_id' => $order_id
//                            ));
//                        $order_url = str_replace('api/notify/wxpay.php', '', $order_url);
//                        $order_url = urlencode(base64_encode($order_url));
//                        send_wechat_message('pay_remind', '', $order_trade_no[0] . ' 订单已支付', $order_url, $order_trade_no[0]);
//                    }
                }
               
            } else {
                $returndata['return_code'] = 'FAIL';
                $returndata['return_msg'] = '签名失败';
            }
        } else {
            $returndata['return_code'] = 'FAIL';
            $returndata['return_msg'] = '无数据返回';
        }
        // 数组转化为xml
        $xml = "<xml>";
        foreach ($returndata as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
        }
        $xml .= "</xml>";

        echo $xml;
        exit();
    }

    function trimString($value)
    {
        $ret = null;
        if (null != $value) {
            $ret = $value;
            if (strlen($ret) == 0) {
                $ret = null;
            }
        }
        return $ret;
    }

    /**
     * 作用：产生随机字符串，不长于32位
     */
    public function createNoncestr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i ++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 作用：设置请求参数
     */
   public function setParameter($parameter, $parameterValue)
    {
        $this->parameters[$this->trimString($parameter)] = $this->trimString($parameterValue);
    }

    /**
     * 作用：生成签名
     */
    public function getSign($Obj)
    {
        foreach ($Obj as $k => $v) {
            $Parameters[$k] = $v;
        }
        // 签名步骤一：按字典序排序参数
        ksort($Parameters);

        $buff = "";
        foreach ($Parameters as $k => $v) {
            $buff .= $k . "=" . $v . "&";
        }
        $String;
        if (strlen($buff) > 0) {
            $String = substr($buff, 0, strlen($buff) - 1);
        }
        // echo '【string1】'.$String.'</br>';
        // 签名步骤二：在string后加入KEY
        $String = $String . "&key=" . $this->payment['wxpay_key'];
        // echo "【string2】".$String."</br>";
        // 签名步骤三：MD5加密
        $String = md5($String);
        // echo "【string3】 ".$String."</br>";
        // 签名步骤四：所有字符转为大写
        $result_ = strtoupper($String);
        // echo "【result】 ".$result_."</br>";
        return $result_;
    }

    /**
     * 作用：以post方式提交xml到对应的接口url
     */
//    public function postXmlCurl($xml, $url, $second = 30)
//    {
//        // 初始化curl
//        $ch = curl_init();
//        // 设置超时
//        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
//        // 这里设置代理，如果有的话
//        // curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
//        // curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
//        // 设置header
//        curl_setopt($ch, CURLOPT_HEADER, FALSE);
//        // 要求结果为字符串且输出到屏幕上
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//        // post提交方式
//        curl_setopt($ch, CURLOPT_POST, TRUE);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
//        // 运行curl
//        $data = curl_exec($ch);
//        // 返回结果
//        if ($data) {
//            curl_close($ch);
//            return $data;
//        } else {
//            $error = curl_errno($ch);
//            echo "curl出错，错误码:$error" . "<br>";
//            curl_close($ch);
//            return false;
//        }
//    }

    /**
     * 获取prepay_id
     */
    public function getPrepayId()
    {
        // 设置接口链接
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        try {
            // 检测必填参数
            if ($this->parameters["out_trade_no"] == null) {
                throw new \Exception("缺少统一支付接口必填参数out_trade_no！" . "<br>");
            } elseif ($this->parameters["body"] == null) {
                throw new \Exception("缺少统一支付接口必填参数body！" . "<br>");
            } elseif ($this->parameters["total_fee"] == null) {
                throw new \Exception("缺少统一支付接口必填参数total_fee！" . "<br>");
            } elseif ($this->parameters["notify_url"] == null) {
                throw new \Exception("缺少统一支付接口必填参数notify_url！" . "<br>");
            } elseif ($this->parameters["trade_type"] == null) {
                throw new \Exception("缺少统一支付接口必填参数trade_type！" . "<br>");
            } elseif ($this->parameters["trade_type"] == "JSAPI" && $this->parameters["openid"] == NULL) {
                throw new \Exception("统一支付接口中，缺少必填参数openid！trade_type为JSAPI时，openid为必填参数！" . "<br>");
            }
            $this->parameters["appid"] = $this->payment['wxpay_appid']; // 公众账号ID
            $this->parameters["mch_id"] = $this->payment['wxpay_mchid']; // 商户号
            $this->parameters["spbill_create_ip"] = $_SERVER['REMOTE_ADDR']; // 终端ip
            $this->parameters["nonce_str"] = $this->createNoncestr(); // 随机字符串
            $this->parameters["sign"] = $this->getSign($this->parameters); // 签名
            $xml = "<xml>";
            foreach ($this->parameters as $key => $val) {
                if (is_numeric($val)) {
                    $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
                } else {
                    $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
                }
            }
            $xml .= "</xml>";
        } catch (\Exception $e) {
            die($e->getMessage());
        }

        // $response = $this->postXmlCurl($xml, $url, 30);
        $response = \Think\Http::curlPost($url, $xml, 30);
        $result = json_decode(json_encode(simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        //E(var_export($result, true));
        if (APP_DEBUG) {
            trace($result, 'prepay', 'DEBUG',true);
            trace($_SESSION, 'openid', 'DEBUG',true);
        }
        $prepay_id = $result["prepay_id"];
        return $prepay_id;
    }

    /**
     * 作用：设置jsapi的参数
     */
    public function getParameters($prepay_id)
    {
        $jsApiObj["appId"] = $this->payment['wxpay_appid'];
        $timeStamp = time();
        $jsApiObj["timeStamp"] = "$timeStamp";
        $jsApiObj["nonceStr"] = $this->createNoncestr();
        $jsApiObj["package"] = "prepay_id=$prepay_id";
        $jsApiObj["signType"] = "MD5";
        $jsApiObj["paySign"] = $this->getSign($jsApiObj);
        $this->parameters = json_encode($jsApiObj);

        return $this->parameters;
  }
}
