<?php

/**
 * 用户模型
 *
 * @author lihengchen@baobeimt.com
 * @date 2016-4-8
 */

namespace Api\Model;

use Alidayu\AlidayuClient as Client;
use Alidayu\Request\SmsNumSend;
use Think\Model\RelationModel;

class UsersModel extends  RelationModel {
    
       //BD 一对一关联 
    protected $_link = [
        
        'BDs' => [
            'mapping_type' => self::HAS_ONE,
            'class_name' => 'user_bds',
            'mapping_name' => 'bd',
            'foreign_key' => 'user_id',
        ],
        //门店信息
        'Stores' => [
            'mapping_type' => self::HAS_ONE,
            'class_name' => 'UserStores',
            'mapping_name' => 'stores',
            'foreign_key' => 'user_id',
        ],
    ];

    /**
     *   用户登录
     * @access public
     * @param string $username 
     * @param string $password 
     */
    public function dologin($username, $password) {

        if (empty($username) || empty($password)) {
            $this->error = '用户名或密码为空';
            return FALSE;
        }

        $con = array('username' => $username);
        $user_model = M('users');
        $res = $user_model->where($con)->find();

        if (count($res) == 0) {
            $this->error = '抱歉，用户不存在';
            return FALSE;

        }

        if ($res['salt']>0) {

            $password=md5(md5($password) . $res['salt']);

        }else{
            $password=md5($password);
        }

        if (!empty($password)  && $password == $res['password']) {

            $store=M('user_stores')->where(['user_id'=>$res['user_id']])->getField('name');

            $openid=D('WechatUser')->where(['bmt_uid'=>$res['user_id']])->getField('openid');

            $BD=M('user_bds')->where(['user_id'=>$res['user_id']])->getField('name');
            
            if (empty($store) && empty($BD)) {
                 $this->error = '必须是商家或者BD用户才能登录';
                 return false;
            }

            $_SESSION['BMT_UID'] = $res['user_id'];
            $_SESSION['BMT_UNAME'] = $res['username'];
            $_SESSION['BMT_OPENID'] = empty($openid)?'':$openid ;
            $_SESSION['BMT_BDUSER'] = $BD;

            $user_arr = array();
            $user_arr['user_id'] = $res['user_id'];
            $user_arr['username'] = $res['username'];
            $user_arr['isbd'] = empty($BD)?0:1;
            return $user_arr;
        }
        $this->error = '登陆失败';
        return false;
    }
    
    /**
     * 注册用户
     * @param string $username
     * @param string $password
     * @param string $password2    密码复杂度 这个后续需要完善，不能让用户设置简单的密码留下安全隐患
     * @param string $code
     * @param string $store_name  门店名称
     * @param string $master_name 门店负责人
     * @param string $state
     * @param string $city
     * @param string $district
     * @param string $address
     */
    public function doreg($username, $password, $password2, $code/*, $store_name,$master_name, $state, $city, $district, $address*/) {
        $pattern = "/^(13[0-9]|14[0-9]|15[0-9]|17[0-9]|18[0-9])\d{8}$/"; //手机号码验证
       // $pattern1 = '/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,18}$/ ';
        $pattern1 = '/^[0-9A-Za-z]{6,18}$/ ';

        if (empty($code)/* || empty($store_name) || empty($master_name) || empty($state) || empty($city) || empty($district) || empty($address)*/) {

            $this->error = '注册信息不完整';
            return FALSE;
        }
        if (empty($username) || strlen($username) != 11 || !preg_match($pattern, $username)) {

            $this->error = '手机号码格式不正确';
            return FALSE;
        }
        /*if (empty($store_name)  || !preg_match("/^[a-zA-Z0-9\x{4e00}-\x{9fa5}]{2,24}$/u", $store_name)) {

            $this->error = '门店名称长度必须大于2小于24个字，且只能是英文字母数字或者中文字';
            return FALSE;
        }
        
        if (empty($master_name)  || !preg_match("/^[a-zA-Z\x{4e00}-\x{9fa5}]{2,12}$/u", $master_name)) {

            $this->error = '负责人姓名长度必须大于2小于12个字，且只能是英文字母或者中文字';
            return FALSE;
        }*/
        
        if (empty($password) || empty($password2) || !preg_match($pattern1, $password) || !preg_match($pattern1, $password2)) {

            $this->error = '密码必须大于6位[数字和英文]';
            return FALSE;
        }

        if ($password != $password2) {

            $this->error = '两次密码输入不一致';
            return FALSE;
        }

        //手机号 验证码验证
        $mob = session('mobile'); //验证的手机号码
        $truecode = session('truecode'); //真实的验证码
        if (!$mob || !$truecode) {

            $this->error = '手机验证码不正确或已失效，请重新获取';
            return FALSE;
        }
        if ($mob != $username) {

            $this->error = '手机号码与验证号码不同';
            return FALSE;
        }
        if ($truecode != $code) {

            $this->error = '验证码错误';
            return FALSE;
        }
        //查询微信用户表 Bd用户id 为0不允许注册
        $wechat_user = M('wechat_user');
        $openid = $_SESSION['BMT_OPENID'];
        $wx_res = $wechat_user->where("openid='{$openid}' ")->field('bd_user_id')->find();
        if (empty($wx_res) || $wx_res['bd_user_id'] <= 0) {
            $this->error = '非BD推荐不允许注册';
            return FALSE;
        }

        $user_model = M('users');
        $res = $user_model->where("username={$username}")->field('user_id')->find();
        if ($res['user_id'] > 0) {

            $this->error = '该手机号已注册,请更换新的手机号码';
            return FALSE;
        }
        //开启事务
        //$user_model->startTrans();
        
        //写入用户表  
        $data['username'] = $username;
        $data['salt'] = rand(1, 9999);
        $data['password'] = md5(md5($password) . $data['salt']);
        $data['reg_time'] = time();
        $data['mobile'] = $username;
        $user_add = $user_model->add($data);

        //写入门店表
        /*$store_model = M('user_stores');
        $sto_arr['user_id'] = $user_add;
        $sto_arr['bind_bd_user_id'] = $wx_res['bd_user_id'];
        $sto_arr['name'] = $store_name;
        $sto_arr['master_name'] = $master_name;//门店负责人姓名
        $sto_arr['state'] = $state;
        $sto_arr['city'] = $city;
        $sto_arr['district'] = $district;
        $sto_arr['address'] = $address;
        $sto_arr['created'] = time();
        $sto_add = $store_model->add($sto_arr);*/

        if ($user_add/* && $sto_add*/) {

            $user_arr = array();
            $user_arr['user_id'] = $user_add;
            $user_arr['username'] = $username;

           // $user_model->commit();
            
            trace(($_SESSION['BMT_OPENID']), 'doreg', 'DEBUG',TRUE);

            if ($_SESSION['BMT_OPENID']) {
                  M('wechat_user')->where(['openid'=>$_SESSION['BMT_OPENID']])->save(['bmt_uid'=>$user_add]);
            }
            return $user_arr;
        }

        //$user_model->rollback();

        $this->error = '注册失败';
        return FALSE;
    }

    /**
     * 注册用户完完善BD门店信息
     */
    public function regStore($user_id, $store_name,$master_name, $state, $city, $district, $address)
    {
        if (empty($user_id) || empty($store_name) || empty($master_name) || empty($state) || empty($city) || empty($district) || empty($address)) {
            $this->error = '注册信息不完整';
            return FALSE;
        }

        if (empty($store_name)  || !preg_match("/^[a-zA-Z0-9\x{4e00}-\x{9fa5}]{2,24}$/u", $store_name)) {

            $this->error = '门店名称长度必须大于2小于24个字，且只能是英文字母数字或者中文字';
            return FALSE;
        }

        if (empty($master_name)  || !preg_match("/^[a-zA-Z\x{4e00}-\x{9fa5}]{2,12}$/u", $master_name)) {

            $this->error = '负责人姓名长度必须大于2小于12个字，且只能是英文字母或者中文字';
            return FALSE;
        }

        if ($user_id < 0) {
            $this->error('找不到注册用户');
            return FALSE;
        }

        $user_model = M('Users');
        $res = $user_model->where("user_id={$user_id}")->field('user_id')->find();
        if (empty($res['user_id'])) {
            $this->error = '该注册用户信息不存在';
            return FALSE;
        }

        //查询微信用户表 获取Bd用户id
        $wechat_user = M('wechat_user');
        $openid = $_SESSION['BMT_OPENID'];
        $wx_res = $wechat_user->where("openid='{$openid}' ")->field('bd_user_id')->find();
        if (empty($wx_res) || $wx_res['bd_user_id'] <= 0) {
            $this->error = '非BD推荐不允许注册';
            return FALSE;
        }

        //写入门店表
        $store_model = M('user_stores');
        $sto_arr['user_id'] = $user_id;
        $sto_arr['bind_bd_user_id'] = $wx_res['bd_user_id'];
        $sto_arr['name'] = $store_name;
        $sto_arr['master_name'] = $master_name;//门店负责人姓名
        $sto_arr['state'] = $state;
        $sto_arr['city'] = $city;
        $sto_arr['district'] = $district;
        $sto_arr['address'] = $address;
        $sto_arr['created'] = time();
        $sto_add = $store_model->add($sto_arr);

        if ($sto_add) {
            $user_arr = array();
            $user_arr['user_id'] = $sto_add;
            return $user_arr;
        }

        $this->error = '注册店铺失败';
        return FALSE;
    }

    /**
     * 找回密码
     * @access public
     * param string username
     * param string password
     * @param string password2
     * @param string code
     */
    public function rest($username, $password, $password2, $code) {
        $pattern = "/^(13[0-9]|14[0-9]|15[0-9]|17[0-9]|18[0-9])\d{8}$/"; //手机号码简单验证
        //$pattern1 = '/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,12}$/';
        $pattern1 = '/^[0-9A-Za-z]{6,18}$/ ';
        if (empty($username) || strlen($username) != 11 || !preg_match($pattern, $username)) {

            $this->error = '手机号码格式不正确';
            return FALSE;
        }

        if (empty($password) || empty($password2) || !preg_match($pattern1, $password) || !preg_match($pattern1, $password2)) {

            $this->error = '密码必须大于6位[数字和英文]';
            return FALSE;
        }
        if (empty($code)) {

            $this->error = '验证码必填';
            return FALSE;
        }
        if ($password != $password2) {

            $this->error = '两次密码输入不一致';
            return FALSE;
        }
        $user_model = M('users');
        $res = $user_model->where("username={$username}")->field('user_id,salt')->find();
        if (empty($res)) {

            $this->error = '该手机号尚未注册';
            return FALSE;
        }

        //手机号 验证码验证
        $mob = session('mobile'); //验证的手机号码
        $truecode = session('truecode'); //真实的验证码
        if (!$mob || !$truecode) {

            $this->error = '手机验证码不正确或已失效，请重新获取';
            return FALSE;
        }
        if ($mob != $username) {

            $this->error = '手机号码与验证号码不同';
            return FALSE;
        }
        if ($truecode != $code) {

            $this->error = '验证码错误';
            return FALSE;
        }
        $data = array();
        $salt=rand(1, 9999);
        $data['password'] = md5(md5($password) . $salt);
        $data['salt'] =$salt;
        $con = $user_model->where("username={$username}")->save($data);
        if ($con !== false) {

            $arr = array();
            $arr['user_id'] = $res['user_id'];
            $arr['username'] = $username;
            return $arr;
        }

        $this->error = '修改失败';
        return FALSE;
    }

    /**
     * 收货信息模型
     * @param string $mobile 手机号
     */
    public function getaddr($mobile = '') {

        $uid = session('BMT_UID');
        if (empty($uid)) {

            $this->error = '用户没有登录';
            return FALSE;
        }
        if (empty($mobile)) {

            $this->error = '手机号码为空';
            return FALSE;
        }

        $arr = M('user_addrs')->where("user_id={$uid} and mobile={$mobile } ")->find();
        if (!empty($arr)) {

            return $arr;
        }
        $this->error = '暂无用户收货信息';
        return FALSE;
    }

    /**
     * 获取验证码
     * @param string mobile 手机号
     * @param int  $tpl_id 模板
     */
    public function getcode($mobile, $tpl_id) {
        $pattern = "/^(13[0-9]|14[0-9]|15[0-9]|17[0-9]|18[0-9])\d{8}$/"; //手机号码简单验证
        if (empty($mobile) || strlen($mobile) != 11 || !preg_match($pattern, $mobile)) {

            $this->error = '手机号码格式不正确';
            return FALSE;
        }

        if (!key_exists($tpl_id, C('SMS_TPLS'))) {

            $this->error = '短信模板id错误';
            return FALSE;
        }

        //如果是注册，已经注册的手机号不能再次获取验证码
        if ($tpl_id == 'reg') {

            $count = M('users')->where("username={$mobile}")->field('user_id')->count();
            if ($count > 0) {

                $this->error = '手机号已注册,请更换新的手机号码';
                return FALSE;
            }
        }

        $smsParams = [];
        foreach (C('SMS_TPLS.' . $tpl_id)['vars'] as $k => $v) {
            $smsParams[$k] = $v;
        }
        $smsParams['code'] = $this->randString();
        $client = new Client;
        $request = new SmsNumSend;
        $req = $request->setSmsTemplateCode(C('SMS_TPLS.' . $tpl_id)['id'])
                ->setRecNum($mobile)
                ->setSmsParam(json_encode($smsParams))
                ->setSmsFreeSignName(C('MsgSign'))
                ->setSmsType('normal')
                ->setExtend('demo');
        $res = $client->execute($req);
        if ($res['err_code'] == 0) {
            session('mobile', $mobile);
            session('truecode', $smsParams['code']);

            //  $this->succss = '验证码发送成功';
            return '验证码发送成功'; //'验证码发送成功'
        }

        $this->error = '验证码发送失败';
        return FALSE;
    }
    
    /**
     * 检测验证码是否正常
     * @param type $mobile
     * @param type $code
     */
    public function checkCode($mobile, $code) {
        
         //手机号 验证码验证
        $mob = session('mobile'); //验证的手机号码
        $truecode = session('truecode'); //真实的验证码
        if (!$mob || !$truecode) {

            $this->error = '手机验证码不正确或已失效，请重新获取';
            return FALSE;
        }
        if ($mob != $mobile) {

            $this->error = '手机号码与验证号码不同';
            return FALSE;
        }
        if ($truecode != $code) {

            $this->error = '验证码错误';
            return FALSE;
        }
        
        return TRUE;
              
    }

    /**
     * 获取随机位数数字
     * @param  integer $len 长度
     * @return string       
     */
    protected static function randString($len = 6) {
        $chars = str_repeat('0123456789', $len);
        $chars = str_shuffle($chars);
        $str = substr($chars, 0, $len);
        return $str;
    }

    /**
     * 获取用户信息
     * @param $userId
     * @return mixed
     */
    public function getUserInfo($userId)
    {
        $condition = array(
            'user_id' => $userId,
        );
        return $this->where($condition)
            ->field($this->getDbFields())
            ->find();
    }

    /*
     * 通过用户openid 获取昵称和头像
     * @return arr
     */
    public function getwx() {
        $model = M('wechat_user');
        $openid = $_SESSION['BMT_OPENID'];
        $arr = $model->where("openid='{$openid}' ")->field('nickname,headimgurl')->find();
        if (!empty($arr)) {
            return ['nickname' => $arr['nickname'], 'avatar_url' => $arr['headimgurl']];
        } else
            return ['nickname' => '', 'avatar_url' => ''];
    }
}
