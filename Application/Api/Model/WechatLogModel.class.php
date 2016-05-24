<?php
/**
 * 微信回调记录
 * @author: peanut
 * @date: 2016-04-18
 * @time: 19:35
 */
namespace Api\Model;

class WechatLogModel extends WechatBaseModel
{
    /**
     * 添加一条信息记录
     * @param $text
     * @return mixed
     */
    public function addOne($text)
    {
        $addData = array(
            'callback' => $text,
        );
        return $this->add($addData);
    }
}