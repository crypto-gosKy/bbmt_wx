<?php
/**
 * @author: peanut
 * @date: 2016-04-19
 * @time: 09:53
 */
namespace Api\Model;

class WechatUserModel extends WechatBaseModel
{
    public function getUserByOpenId($openid)
    {
        $condition = array(
            'openid' => $openid,
        );
        return $this->where($condition)
            ->field($this->getDbFields())
            ->find();
    }

    /**
     * 把微信用户与宝贝码头用户绑定
     * @param $openid
     * @param $bmtUid
     * @return bool
     */
    public function setUserUid($openid, $bmtUid)
    {
        $condition = array(
            'openid' => $openid,
        );
        $saveData = array(
            'bmt_uid' => $bmtUid,
        );
        return $this->where($condition)
            ->setField($saveData);
    }

    /**
     * 更新用户信息
     * @param $openId
     * @param $data
     * @return bool
     */
    public function updateUserInfo($openId, $data)
    {
        $condition = array(
            'openid' => $openId,
        );
        return $this->where($condition)
            ->save($data);
    }

    /**
     * 用户取消关注
     * @param $openId
     * @return bool
     */
    public function unsubscribe($openId)
    {
        $condition = array(
            'openid' => $openId,
        );
        return $this->where($condition)
            ->setField('subscribe', 0);
    }

    /**
     * 绑定bd用户
     * @param $openid
     * @param $bdUid
     * @return bool
     */
    public function bindBdUser($openid, $bdUid)
    {
        $condition = array(
            'openid' => $openid,
        );
        $saveData = array(
            'bd_user_id' => $bdUid,
        );
        return $this->where($condition)
            ->save($saveData);
    }
}