<?php
/**
 * @author: peanut
 * @date: 2016-04-19
 * @time: 09:53
 */
namespace Api\Logic;

use Api\Model\WechatUserModel;

class WechatUserLogic extends WechatUserModel
{
    /**
     * 记录微信用户
     * @param $openId
     * @param $wechatRevData
     * @return bool
     */
    public function recordUserInfo($openId, $wechatRevData)
    {
        $saveData = $this->create($wechatRevData);
        if (!$this->getUserByOpenId($openId)) {
            return $this->add($saveData);
        } else {
            return $this->updateUserInfo($openId, $saveData);
        }
    }

    /**
     * 绑定用户
     * @param $openId
     * @param $bmtUid
     * @return bool|int
     */
    public function bindUser($openId, $bmtUid)
    {
        $info =$this->getUserByOpenId($openId);
        if ($info['bmt_uid'] != $bmtUid) {
            return $this->setUserUid($openId, $bmtUid);
        }
        return 1;
    }
}