<?php
/**
 * @author: peanut
 * @date: 2016-04-19
 * @time: 11:36
 */
namespace Api\Model;

class WechatQrcodeModel extends WechatBaseModel
{
    public function getOneByScene($sceneId)
    {
        $condition = array(
            'scene_id' => $sceneId,
        );
        return $this->where($condition)
            ->field($this->getDbFields())
            ->find();
    }

    /**
     * 增加扫描量
     * @param $sceneId
     * @return bool
     */
    public function setIncScan($sceneId)
    {
        $condition = array(
            'scene_id' => $sceneId,
        );
        return $this->where($condition)
            ->setInc('scan_num');
    }

    public function getScanFunction($sceneId)
    {
        $condition = array(
            'scene_id' => $sceneId,
        );
        return $this->where($condition)
            ->field('function')
            ->find();
    }
}