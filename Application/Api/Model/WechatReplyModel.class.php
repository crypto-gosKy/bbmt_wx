<?php
/**
 * 自动回复模型
 * @author: peanut
 * @date: 2016-04-14
 * @time: 20:41
 */
namespace Api\Model;

class WechatReplyModel extends WechatBaseModel
{
    public function getOne($id)
    {
        $condition = array(
            'id' => $id,
        );
        return $this->where($condition)
            ->field($this->getDbFields())
            ->find();
    }

    public function getReply($type,$wechatId)
    {
        $condition = array(
            'wechat_id' => $wechatId,
            'type' => $type,
        );
        return $this->where($condition)
            ->field($this->getDbFields())
            ->find();
    }
}

class WechatReply
{
    //微信公众号id
    public $weChatId = 0;
    //回复方式:subscribe(关注回复),msg(消息自动回复),keywords(关键词自动回复)
    public $type = '';
    //回复内容，text必须
    public $content = '';
    //多媒体id，image、video、voice这三种类型需要填写
    public $mediaId = 0;
    //关键词回复规则名
    public $ruleName = '';
    //消息回复类型，text、image、video、voice
    public $replyType = '';
}