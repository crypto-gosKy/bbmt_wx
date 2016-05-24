<?php
/**
 * @author: peanut
 * @date: 2016-04-20
 * @time: 13:38
 */
namespace Api\Model;

use Think\Model;

class UserBdsModel extends Model
{
    public function getOne($userId)
    {
        $condition = array(
            'user_id' => $userId,
        );
        return $this->where($condition)
            ->field($this->getDbFields())
            ->find();
    }
}