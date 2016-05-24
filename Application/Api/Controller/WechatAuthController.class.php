<?php

/**
 * @author: peanut
 * @date: 2016-04-20
 * @time: 12:56
 */

namespace Api\Controller;

use Api\Logic\WechatUserLogic;
use Api\Model\UserBdsModel;
use Api\Model\UsersModel;
use Api\Model\WechatModel;
use Think\Controller;

class WechatAuthController extends WechatBaseController {

    public function index() {
        if (empty($_GET['route'])) {
            $_GET['route'] = 1;
        } else {
            $_GET['route'] = intval($_GET['route']);
        }

        if (empty($_SESSION['BMT_OPENID'])) {

            $this->wxOauth();
        }
        
         $tourl=C('route_url')[100];//默认到登陆页面

        $wechatUser = new WechatUserLogic();

        $w_userInfo = $wechatUser->getUserByOpenId($_SESSION['BMT_OPENID']);

        if ($w_userInfo  &&   $_GET['route'] !==100  ) {//存在这个微信用户
           
            if ($w_userInfo['bmt_uid']) {//已经绑定BMT_UID，

                //$this->recordUserInfo();

                $this->login($w_userInfo['bmt_uid']);
 
                $tourl=C('route_url')[$_GET['route']];//跳转到自定义页面
                  
                
            } elseif ($w_userInfo['bd_user_id']) {//通过BD二维码扫描过来的


                $tourl=C('route_url')[0];//跳转到注册页面
                
            }
            
        }
        
         
        // header('Location:' . $tourl  );exit;
          echo '<meta http-equiv=refresh content=0;URL="' . $tourl . '">';
           
    }

    public function login($userId) {

        $userModel = new UsersModel();
        $userInfo = $userModel->getUserInfo($userId);

        $userBd = new UserBdsModel();
        $bdInfo = $userBd->getOne($userId);

        $_SESSION['BMT_UID'] = $userId;
        $_SESSION['BMT_UNAME'] = $userInfo['username'];
        $_SESSION['BMT_BDUSER'] = empty($bdInfo['name']) ? $bdInfo['name'] : '';
    }

    public function recordUserInfo() {

        if (is_weixin_browser() && !empty($_SESSION['BMT_OPENID'])) {
            $userInfo = $this->weChatObj->getUserInfo($_SESSION['BMT_OPENID']);
            $wechatLogic = new WechatUserLogic();
            $wechatLogic->recordUserInfo($_SESSION['BMT_OPENID'], $userInfo);
        }
    }

    public function wxOauth() {

        $backurl = U('WechatAuth/index', array('route' => $_GET['route']), true, true);

//        if (is_weixin_browser() ) {

        if (empty($_GET['code'])) {
            // 开始微信授权登录

            $url2 = $this->weChatObj->getOauthRedirect($backurl, 1);

            header("Location: " . $url2 . "\n");
            //echo '<meta http-equiv=refresh content=1;URL="' . $url2 . '">';
            exit();
        } else {
            // 用code换token

            $token = $this->weChatObj->getOauthAccessToken();
            
            trace($token, 'wxtoken' ,'DEBUG', true );
            
            if ($token['openid']) {

                session('BMT_OPENID', $token['openid']);
                //setcookie('BMT_OPENID', $token['openid'],  time()+86400,'','baobeimt.cn');
            }
        }
    }

}
