<?php
/**
 * @author: peanut
 * @date: 2016-04-19
 * @time: 11:39
 */
namespace Api\Logic;

use Api\Model\WechatQrcodeModel;
use Api\Model\WechatUserModel;

class WechatQrcodeLogic extends WechatQrcodeModel
{
    public function updateScan($sceneId)
    {
        $res =$this->getOneByScene($sceneId);
        if ($res) {
            return $this->setIncScan($sceneId);
        }
        return false;
    }

    public function bindBdUser($openId, $sceneId)
    {
        $userModel = new WechatUserModel();
        return $userModel->bindBdUser($openId, $sceneId);
    }


}