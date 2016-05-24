<?php
/**
 * @author: peanut
 * @date: 2016-04-19
 * @time: 17:43
 */
namespace Api\Model;

class WechatRuleKeywordsModel extends WechatBaseModel
{
    public function getOne($keyword)
    {
        $condition = array(
            'rule_keywords' => $keyword,
        );
        return $this->where($condition)
            ->field($this->getDbFields())
            ->find();
    }
}