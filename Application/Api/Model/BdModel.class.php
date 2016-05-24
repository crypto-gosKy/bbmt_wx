<?php

/**
 * Bd模型
 * @author lihengchen@baobeimt.com
 * @date 2016-4-19
 */

namespace Api\Model;

class BdModel extends BaseModel {

    protected $tableName = 'wechat_qrcode';

    /**
     * 获取我的二维码
     */
    public function getmycode() {
        $model = M('user_bds');
        $uid = (int)$_SESSION['BMT_UID'];
        $arr = $model->where("user_id ={$uid}")->field('qrcodeurl')->find();
        if ($arr) {
            return ['qrcode_url' => str_replace('https://', 'http://', $arr['qrcodeurl'])];   
        }
        return ['qrcode_url' => ''];
    }

    public function getwx() {
        $model = M('wechat_user');
        $uid = $_SESSION['BMT_UID'];
        $arr = $model->where("bmt_uid={$uid}")->field('nickname,headimgurl')->find();
        if (!empty($arr)) {
            return ['nickname' => $arr['nickname'], 'avatar_url' => $arr['headimgurl']];
        } else
            return ['nickname' => '', 'avatar_url' => ''];
    }

}
