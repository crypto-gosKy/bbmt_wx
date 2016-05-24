<?php

/**
 * 订单【接口】
 * @author [chan] <[<maclechan@qq.com>]>
 * @date(2016-04-08)
 */

namespace Api\Controller;

use Api\Model\UserStoresModel;

class ShopsController extends BaseController
{
    /**
     * 微店信息
     */
    public function info()
    {
        $uid = (int)$_SESSION['BMT_UID'];
        $res = D('UserStores')->where("user_id=$uid")->find();

        if ($res) {
            $data = [
                'shop_name' => $res['name'],
                'shop_contacts' => $res['master_name'],
                'shop_city' => $res['state'].$res['city'].$res['district'],
            ];
        }

        $this->echoJSON($data);
    }

    /*
     * 微店二维码
     * 需求不明确暂无法做
     */
    /*public function qr_code() {
        $model = D('Bd');
        $res = $model->getmycode();
        $this->echoJSON($res, D('Bd'));
    }*/

    /**
     * 客户设置
     */
    public function service()
    {
        $uid = (int)$_SESSION['BMT_UID'];

        $store_id = intval(I('store_id',0));
        $service_phone = I('service_phone',0);

        $store = D('UserStores');

        if ($store_id <1 || $store_id!=$uid) {
            $this->echoJSON(FALSE,'提交信息有错');
        }

        if (empty($service_phone)) {
            $this->echoJSON(FALSE,'客服电话不能为空');
        }

        $res = D('StoreService')->addOne($store_id, $service_phone);

        $this->echoJSON($res);
    }

}
