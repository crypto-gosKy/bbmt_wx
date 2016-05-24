<?php
/**
 * @author: peanut
 * @date: 2016-04-19
 * @time: 10:17
 */
namespace Api\Logic;

use Api\Model\WechatReplyModel;
use Api\Model\WechatRuleKeywordsModel;

class WechatReplyLogic extends WechatReplyModel
{
    /**
     * 关注回复
     * @param $wechatId
     * @return mixed
     */
    public function subscribeReply($wechatId)
    {
        $res =  $this->getReply('subscribe',$wechatId);
        return $res['content'];
    }

    /**
     * 消息回复
     * @param $wechatId
     * @return mixed
     */
    public function msgReply($wechatId)
    {
        $res = $this->getReply('msg',$wechatId);
        return $res['content'];
    }

    /**
     * 关键词回复
     * @param $keyword
     * @param $wechatId
     * @return mixed
     */
    public function keywordsReply($keyword,$wechatId)
    {
        $ruleModel = new WechatRuleKeywordsModel();
        $rule = $ruleModel->getOne($keyword);
        $res = $this->getOne($rule['rid']);
        if (empty($res)) {
            $res = $this->getReply('msg',$wechatId);
        }
        return $res['content'];
    }
}