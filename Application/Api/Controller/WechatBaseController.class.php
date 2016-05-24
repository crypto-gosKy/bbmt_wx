<?php
/**
 * @author: peanut
 * @date: 2016-04-19
 * @time: 09:33
 */
namespace Api\Controller;

use Api\Model\WechatErrorModel;
use Api\Model\WechatLogModel;
use Api\Model\WechatModel;
use Think\Controller;
use Vendor\Wechat\Wechat;

class WechatBaseController extends Controller
{
    protected $weChatObj;
    protected $weChatId = 1;
    protected $token;

    public function __construct()
    {
        
        parent::__construct();
        $this->weChatInit();
    }

    protected function weChatInit()
    {
        
        $weChatModel = new WechatModel();
        $info = $weChatModel->getOne($this->weChatId);
        if (empty($info)) {
            exit('数据库里没有微信');
        }
        $config['token'] = $info['token'];
        $config['appid'] = $info['appid'];
        $config['appsecret'] = $info['appsecret'];
        $this->token = $info['token'];
        $this->weChatObj = new Wechat($config);
       
        //发布上线时，需要验证，
//        $this->weChatObj->valid(false);
    }

    protected function weChatLog()
    {
        $log = new WechatLogModel();
        $rev = $this->weChatObj->getRev()->getRevData();
        if (!empty($rev)) {
            $log->addOne('xml:' . serialize($rev));
        }
        $str = $this->arrayToString($_REQUEST);
        if (!empty($str)) {
            $log->addOne('request:' . $str);
        }
    }

    protected function log($msg)
    {
        if (C('WECHAT_DEBUG_LOG')) {
            $log = new WechatLogModel();
            $log->addOne('debug:' . $msg);
        }
    }

    /**
     * 错误记录
     */
    protected function errorLog()
    {
        $log = new WechatErrorModel();
        $log->log($this->weChatObj->errCode, $this->weChatObj->errMsg);
    }

    /**
     * 数组转字符串
     * @param $arr
     * @return string
     */
    protected function arrayToString($arr)
    {
        $str = '';
        foreach ($arr as $key => $value) {
            $str .= $key . '=>' . $value . ',';
        }
        return $str;
    }
}