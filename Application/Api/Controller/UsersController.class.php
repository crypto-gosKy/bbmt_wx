<?php

/**
 * 用户控制器
 * @author lihengchen@baobeimt.com
 * @date 2016-4-8
 */

namespace Api\Controller;

class UsersController extends BaseController {

    /**
     * 用户登录
     * @access public
     * @param string $username 
     * @param string $password 
     */
    public function login() {

        $res = D('Users')->dologin(I('username', ''), I('password', '')); //登录
        $this->echoJSON($res, D('Users'));
    }

    /**
     * 注册用户
     * @param string $username
     * @param string $password
     * @param string $password2
     * @param string $code
     * @param string $store_name
     * @param string $state
     * @param string $city
     * @param string $district
     * @param string $address
     */
    public function reg()
    {
        $res = D('Users')->doreg(I('username', ''), I('password', ''), I('password2', ''), I('code', ''));
        $this->echoJSON($res, D('Users'));
    }

    /**
     * 注册用户完善店铺信息
     */
    public function regStore()
    {
        $res = D('Users')->regStore(I('user_id', ''), I('store_name', ''), I('master_name', ''), I('state', ''), I('city', ''), I('district', ''), I('address', ''));
        $this->echoJSON($res, D('Users'));
    }

    /**
     * 找回密码
     * @access $public
     * param string $username
     * param string $password
     * @param string $password2
     * @param string $code
     */
    public function restpwd() {
        $res = D('Users')->rest(I('username', ''), I('password', ''), I('password2', ''), I('code', ''));
        $this->echoJSON($res, D('Users'));
    }

    /**
     * 获取验证码
     * @param string $mobile 手机号
     */
    public function getcode() {
        $res = D('Users')->getcode(I('mobile', ''), I('tpl_id', ''));
        $this->echoJSON($res, D('Users'));
    }

    /**
     * 获取当前用户收货信息
     * @access public 
     * @param string $mobile 用户手机号码
     */
    public function address() {
        $res = D('Users')->getaddr(I('mobile', ''));
        $this->echoJSON($res, D('Users'));
    }

    
    /**
     * 校验手机验证码；
     */
    public function checkcode()
    {
        $res = D('Users')->checkCode(I('mobile', ''), I('code', ''));
        $this->echoJSON($res, D('Users'));
    }
    /**
     * 获取微信用户头像及昵称
     * return json
     */
    public function wxinfo() {
        $res = D('Users')->getwx();
        $this->echoJSON($res, D('Users'));
    }

}
