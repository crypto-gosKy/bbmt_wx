<?php
/**
 * @author: peanut
 * @date: 2016-04-19
 * @time: 10:56
 */
namespace Api\Model;

class WechatErrorModel extends WechatBaseModel
{
    public function log($errorCode, $errorMsg)
    {
        $addData = array(
            'error_code' => $errorCode,
            'error_msg' => $errorMsg,
        );
        return $this->add($addData);
    }
}