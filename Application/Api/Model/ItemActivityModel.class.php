<?php
/**
 * @author: peanut
 * @date: 2016-04-14
 * @time: 10:49
 */
namespace Api\Model;

use Think\Model;

class ItemActivityModel extends Model
{
    /**
     * 获取所有活动信息
     * @param $wechatId
     * @return mixed
     */
    public function getAll()
    {
        $condition['status'] = 1;
        $condition['scene'] = 1;
        return $this->where($condition)
            ->order(' item_activity_id desc ')
            ->select();
    }

    /**
     * 重组活动列表信息
     * @return mixed
     */
    public function activeList(){
        if(($datas=$this->getAll())) {
            foreach ($datas as $k => $v) {
                $aclist[$k]['item_activity_id'] = $v['item_activity_id'];
                $aclist[$k]['name'] = $v['name'];
                $aclist[$k]['url'] = $v['url'];
            }
            return ['trade_list'=>$aclist];
        }
        $this->error='Sorry！活动已结束';
        return FALSE;
    }
}