<?php
/**
 * @author: peanut
 * @date: 2016-04-14
 * @time: 10:49
 */
namespace Api\Model;

use Think\Model;

class WechatModel extends WechatBaseModel
{
    public function getOne($wechatId)
    {
        $condition = array(
            'id' => $wechatId,
        );
        return $this->where($condition)
            ->field($this->getDbFields())
            ->find();
    }

    public function getAuthDirect()
    {
        $res =  $this->field('oauth_redirecturi')
            ->find();
        return $res['oauth_redirecturi'];
    }
}