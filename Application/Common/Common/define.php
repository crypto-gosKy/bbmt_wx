<?php


//定义系统全局常量
/**
 * 国内商品交易
 */
define('TRADE_TYPE_CN', 0);


/**
 * 保税区商品交易
 */
define('TRADE_TYPE_FTZ', 1);


/**
 * 来自微信的订单
 */
define('ORDER_FROM_WEIXIN', 'weixin');


/**
 * 交易状态
 * 交易关闭 （未付款24小时超时交易自动关闭 ，未付款管理员手工关闭， 清关失败关闭）
 */
define('TRADE_CLOSED', 0);

/**
 * 交易状态
 * 等待买家付款（即：未付款）
 */
define('WAIT_BUYER_PAY', 1);
/**
 * 交易状态
 * 等待海关清关（即：已付款）
 */
define('WAIT_CUSTOM_CLEARANCE', 4);
/**
 * 交易状态
 * 等待卖家（供应商）发货,（即：已经海关清关完成）
 */
define('WAIT_SELLER_SEND_GOODS', 8);
/**
 * 交易状态
 * 等待买家确认收货,（即：卖家（供应商）已发货）
 */
define('WAIT_BUYER_CONFIRM_GOODS', 10);
/**
 * 交易状态
 * 交易成功 (买家已收货)
 */
define('TRADE_FINISHED', 100);

/**
 * 首单立减9.9元 (货币单位：分)
 */
define('TRADE_FIRST', 990);

/**
 * 首单消费200 减9.9元
 */
define('TRADE_FIRST_CONDITION', 20000);

