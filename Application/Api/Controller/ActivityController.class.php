<?php
/**
 * 活动类
 * @author [chan] <[<maclechan@qq.com>]>
 * @date(2016-04-28)
 */
namespace Api\Controller;

use Api\Model\ItemActivityModel;

class ActivityController extends BaseController
{
    /**活动列表【接口】
     * @return JSON $data 活动信息
     */
    public function active_list()
    {
        $active = D('ItemActivity');
        $result =$active->activeList();
        $this->echoJSON($result,$active);
    }

}