<?php

/**
 * 客服电话模型
 */

namespace Api\Model;

use Think\Model\RelationModel;

class StoreServiceModel extends RelationModel
{
    /**商家客服添加
     * @param $store_id
     * @param $service_phone
     * @return mixed
     */
    public function addOne($store_id, $service_phone)
    {
        $data = [
            'store_id' => $store_id,
            'service_phone' => $service_phone,
        ];

        $store_ids = $this->field('store_id')->where("store_id=$store_id")->find();

        if (in_array($store_id, $store_ids)) {
            //存在就修改
            $this->where("store_id=$store_id")->save($data);
            return $data;

        }else{
             $this->add($data);
             return $data;
        }
    }
}
