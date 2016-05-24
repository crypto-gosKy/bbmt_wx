<?php

/**
 *  订单信息,交易的商品详情模型
 *  @author [chan] <[<maclechan@qq.com>]>
 *  @date(2016-04-06)
 */

namespace Common\Model;

use Think\Model\RelationModel;

class TradeOrdersModel extends RelationModel {
    //关联关系
    protected $_link = [
        //交易
        'Trades' => [
            'mapping_type' => self::BELONGS_TO,
            'class_name' => 'Trades',
            'mapping_name' => 'Trades',
            'foreign_key' => 'tid',
        ]
    ];
    
}