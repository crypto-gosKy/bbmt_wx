<?php
/**
 *  sku
 * @author [chan] <[<maclechan@qq.com>]>
 * @date(2016-04-11)
 */

namespace Common\Model;

use Think\Model\RelationModel;

class ItemSkusModel extends RelationModel
{
    //关联Item关系
    protected  $_link = [
        //商品详情
        'Items' => [
            'mapping_type' => self::BELONGS_TO,
            'class_name' => 'Items',
            'mapping_name' => 'items',
            'foreign_key' => 'item_id',
        ],
    ];
}