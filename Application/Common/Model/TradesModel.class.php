<?php

/**
 *  交易信息模型
 *  @author [chan] <[<maclechan@qq.com>]>
 *  @date(2016-04-06)
 */

namespace Common\Model;

use Think\Model\RelationModel;

class TradesModel extends RelationModel {

    //数据校验
    protected $_validate = [
        ['buyer_user_id', '', '会员id不能为空', self::EXISTS_VALIDATE, 'require', self::MODEL_INSERT],
    ];

    /**
     * 获取交易订单
     * @param int $page=1当前页
     * @return array $data 返回订单列表
     */
    public function getTrades($page) {
        //分页偏移设置
        $length = 5;
        $start = $page > 0 ? (($page - 1) * $length) : 0;

        $TradesModel = new TradesModel();
        $user_id = (int) $_SESSION['BMT_UID']; //用户ID
        $trade = $TradesModel->relation(true)->where("buyer_user_id=$user_id")->order('tid desc')->limit($start, $length)->select();
        $count = $TradesModel->relation(true)->where("buyer_user_id=$user_id")->count();
        if ($trade) {
            $trades = [];
            foreach ($trade as $k => $v) {
                $trades[$k]['tid'] = $v['tid'];
                $trades[$k]['pay_amount'] = bmt_format_money($v['pay_amount']);
                $trades[$k]['trade_status'] = (int) $v['status'];
                $trades[$k]['created'] = $v['created'] ? date('Y-m-d H:i:s', $v['created']) : '';
                $trades[$k]['pay_time'] = $v['pay_time'] ? date('Y-m-d H:i:s', $v['pay_time']) : '';
                $trades[$k]['cc_time'] = $v['cc_time'] ? date('Y-m-d H:i:s', $v['cc_time']) : '';
                $trades[$k]['consign_time'] = $v['consign_time'] ? date('Y-m-d H:i:s', $v['consign_time']) : '';
                $trades[$k]['end_time'] = $v['end_time'] ? date('Y-m-d H:i:s', $v['end_time']) : '';
                $trades[$k]['trade_type'] = (int) $v['trade_type'];
                $trades[$k]['post_fee'] = bmt_format_money($v['post_fee']);
                //定单表
                foreach ($v['Torder'] as $_k => $_v) {
                    $trades[$k]['orders'][$_k]['title'] = $_v['title'];
                    $trades[$k]['orders'][$_k]['sku_spec_name'] = $_v['sku_spec_name'];
                    $trades[$k]['orders'][$_k]['price'] = bmt_format_money($_v['price']);
                    $trades[$k]['orders'][$_k]['num'] = (int) $_v['num'];
                    $trades[$k]['orders'][$_k]['total_fee'] = bmt_format_money($_v['total_fee']);
                    $trades[$k]['orders'][$_k]['pic_url'] = $_v['pic_url'];
                    $trades[$k]['orders'][$_k]['logistics_company'] = empty($_v['logistics_company']) ? '' : $_v['logistics_company'];
                    $trades[$k]['orders'][$_k]['invoice_no'] = empty($_v['invoice_no']) ? '' : $_v['invoice_no'];
                }
            }
            return ['trade_list' => $trades, 'total_page_num' => ceil($count / $length)];
        }
        $this->error = '暂无订单';
        return FALSE;
    }

    public function getDetail($tid = 0) {

        $TradesModel = new TradesModel();
        $trade = $TradesModel->relation(true)->where(['buyer_user_id' => (int) $_SESSION['BMT_UID'], 'tid' => $tid])->find();
        if (!empty($trade)) {
            $detail = [
                'tid' => $trade['tid'],
                'total_fee' => bmt_format_money($trade['total_fee']),
                'pay_amount' => bmt_format_money($trade['pay_amount']),
                'discount_fee' =>$trade['discount_fee']>0?bmt_format_money($trade['discount_fee']):0,
                'post_fee' => bmt_format_money($trade['post_fee']),
                'trade_status' => (int) $trade['status'],
                'created' => $trade['created'] ? date('Y-m-d H:i:s', $trade['created']) : '',
                'pay_time' => $trade['pay_time'] ? date('Y-m-d H:i:s', $trade['pay_time']) : '',
                'cc_time' => $trade['cc_time'] ? date('Y-m-d H:i:s', $trade['cc_time']) : '',
                'consign_time' => $trade['consign_time'] ? date('Y-m-d H:i:s', $trade['consign_time']) : '',
                'end_time' => $trade['end_time'] ? date('Y-m-d H:i:s', $trade['end_time']) : '',
                'trade_type' => intval($trade['trade_type']),
                'shippingaddr_name' => $trade['Taddr']['name'],
                'shippingaddr_mobile' => $trade['Taddr']['mobile'],
                'shippingaddr_state' => $trade['Taddr']['state'],
                'shippingaddr_city' => $trade['Taddr']['city'],
                'shippingaddr_district' => $trade['Taddr']['district'],
                'shippingaddr_address' => $trade['Taddr']['address'],
                'shippingaddr_idcard' => $trade['Taddr']['idcard'],
                    //'close_mark'=>  empty($trade['close_mark'])?'':$trade['close_mark'],
            ];
            foreach ($trade['Torder'] as $k => $v) {
                $detail['orders'][$k] = [
                    'title' => $v['title'],
                    'sku_spec_name' => $v['sku_spec_name'],
                    'price' => bmt_format_money($v['price']),
                    'num' => (int) $v['num'],
                    'total_fee' => bmt_format_money($v['total_fee']),
                    'pic_url' => $v['pic_url'],
                    'logistics_company' => empty($v['logistics_company']) ? '' : $v['logistics_company'],
                    'invoice_no' => empty($v['invoice_no']) ? '' : $v['invoice_no'],
                    'close_mark' => $v['close_mark'] ? $v['close_mark'] : $trade['close_mark'],
                ];
            }

            return $detail;
        }

        return [];
    }

    //关联关系
    protected $_link = [
        //交易定单详情
        'TradeOrders' => [
            'mapping_type' => self::HAS_MANY, //mapping_type 关联类型
            'class_name' => 'TradeOrders', //class_name 关联的模型类名
            'mapping_name' => 'Torder', //mapping_name ：关联的映射名称，用于获取数据用
            'foreign_key' => 'tid', //关联的外键名称
        //'mapping_fields' => 'num', //关联要查询的个别字段
        ],
        //交易定单地址
        'TradeShippingAddr' => [
            'mapping_type' => self::HAS_ONE,
            'class_name' => 'TradeShippingAddr',
            'mapping_name' => 'Taddr',
            'foreign_key' => 'tid',
        //'as_fields' => 'name',//关联的字段值映射成数据对象中的某个字段
        ]
    ];

    /* 下单数据 下单页展示用；
     * 
     * 输出数据前作如下步骤校验：
     * 
     * 
     * 1、判断商品、规格是否存在；
     * 2、判断商品是否已经下架；
     * 3、商品、规格的库存是否在购买数量内；
     * 
     * @param $item_id 商品id
     * @param $sku_id   sku_id
     * @param $num  购买数量 ,进口商品 这个参数值始终是1
     * @return bool
     * 
     * @author: luoyongyao 2016年4月26日 
     */

    public function getOrderData($item_id = 0, $sku_id = 0, $num = 1, $send_addrs = '') {


        if (!(settype($item_id, 'int') && settype($sku_id, 'int') && settype($num, 'int') )) {

            $this->error = '请检查商品编号、规格、购买数量值是否正确';

            return FALSE;
        }

        if (intval($item_id) < 0) {

            $this->error = '商品不存在';
            return FALSE;
        }

        $ItemsModel = new ItemsModel();
        $goods = $ItemsModel->relation(true)->where("item_id=$item_id")->find();
        if (!$goods || $goods['status'] == 0) {
            $this->error = '商品不存在或者已经下架';
            return FALSE;
        }
        
        //是否活动商品， 则必须是在活动时间内才能购买；
        if ($goods['item_activity_id']>0  &&  $Activity=M('ItemActivity')->find($goods['item_activity_id'])   ) {
           
            if (!$Activity['status']) {
                $this->error = '活动已经停止不能购买';
                return FALSE;
            }
            
            if($Activity['type']==2 ){//限时类活动
                if (empty( $Activity['activity_start_time']  )   ||   $Activity['activity_start_time']> time() ) {  
                    //开始时间大于当前时间 是不允许购买d
                      $this->error = '活动还未开始不能购买';
                      return FALSE;
                }

                 if (empty( $Activity['activity_end_time']  )   ||   $Activity['activity_end_time'] < time() ) {  
                    //结束时间小于当前时间 是不允许购买d
                      $this->error = '活动已经结束不能购买';
                      return FALSE;
                }
            }
                        
        }
        
        //当存在SKU时 $sku_id为必传值
        $t = false;
        if ($goods['Skus'] && $sku_id) {
            //判断SKU表中是否存在传入的 $sku_id
            foreach ($goods['Skus'] as $sku) {
                if ($sku_id == $sku['sku_id']) {
                    $t = true;
                    break;
                }
            }
        }

        if (empty($goods['Skus'])) {//商品如果没有sku  则初始化 spec_name 为空字符串
            $goods['spec_name'] = '';
        } elseif ($t === FALSE) {
            $this->error = '请选择正确的商品规格';
            return FALSE;
        } else {
            $goods['price'] = $sku['price']; //sku 存在的情况下 取 sku的价格 
            $goods['quantity'] = $sku['quantity']; //sku 存在的情况下 取 sku的库存
            $goods['spec_name'] = $sku['spec_name']; //sku 存在的情况下 spec_name 
            $goods['sku_id'] = $sku['sku_id'];
            $goods['supplier_id'] = $sku['supplier_id'];
            $goods['sku_outer_id'] = $sku['sku_outer_id'];
        }

        $type = intval($goods['type']);
        if ($type == 1) {
            $num = intval($num);
            $trade_type = TRADE_TYPE_CN; //国内
        } else if ($type == 2) {
            $num = 1; //保税产品购买数量始终为1
            $trade_type = TRADE_TYPE_FTZ; //保税区
        }

        if ($num < 1) {
            $num = 1;
        }

        //判断库存
        if ($num <= intval($goods['quantity'])) {
            //判断运费  获取收货地址
            $logistics_data = $this->getLogisticsFee($item_id,$send_addrs,$num);

            if(isset($logistics_data['data']['logistics_fee'])){
                $logistics_fee = $logistics_data['data']['logistics_fee'] * 100;
            }else{
                $logistics_fee = 0;
            }

            //判断是否第一次下单
            $has_trade = $this->where(['buyer_user_id'=>$_SESSION['BMT_UID'],'status'=>['GT',0]])->find();
            if ($has_trade) {

                $pay_amount = intval($goods['price']) * $num + $logistics_fee; //实际付款金额

                $total_fee = $pay_amount;
            }else{
                //第一次下单
                $total_fee = intval($goods['price']) * $num + $logistics_fee; //商品总金额

                $discount_fee_condition = TRADE_FIRST_CONDITION;                //首单立减条件金额(分)
                if(intval($goods['price']) * $num >= $discount_fee_condition){
                    $discount_fee = TRADE_FIRST; //优惠金额(分)
                }else{
                    $discount_fee = 0;
                }


                $pay_amount = $total_fee - $discount_fee; //实际付款金额
            }

            $data = [
                'discount_fee' => !empty($discount_fee) ? bmt_format_money($discount_fee) : 0, // 首单立减金额(单位：元)
                '_discount_fee' => !empty($discount_fee) ? $discount_fee : 0, // 首单立减金额(单位：分)
                'pay_amount' => bmt_format_money($pay_amount), // 实际付款金额(元)
                '_pay_amount' => $pay_amount, // 实际付款金额(单位：分)
                'total_fee'=>  bmt_format_money($total_fee),
                'logistics_fee' => bmt_format_money($logistics_fee),
                '_logistics_fee' => $logistics_fee,
                'trade_type' => (int) $trade_type, //交易类型 
                'orders' => [
                    'title' => $goods['title'],
                    'sku_spec_name' => $goods['spec_name'],
                    'price' => bmt_format_money($goods['price']), //商品单价(元)
                    '_price' => $goods['price'], //商品单价(元)
                    'num' => $num, //购买数量
                    'total_fee' => bmt_format_money($total_fee), //商品总金额(元)
                    '_total_fee' => $total_fee, //商品总金额(分)
                    'pic_url' => $goods['pic_url'],
                    'item_id' => $goods['item_id'], //item_id
                    'sku_id' => $goods['sku_id'], //sku_id
                    'supplier_id' => $goods['supplier_id'], //供应商id
                    'sku_outer_id' => $goods['sku_outer_id'], //sku 编号
                    'item_activity_id'=>$goods['item_activity_id'],//活动 id
                ]
            ];
            return $data;
        } else {
            $this->error = '库存不足';
            return FALSE;
        }
    }

    /**
     * 提交订单数据
     * @param int $item_id
     * @param int $sku_id
     * @param int $num
     */
    public function submitOrder($item_id, $sku_id, $num,$addr) {


        if (!(settype($item_id, 'int') && settype($sku_id, 'int') && settype($num, 'int') )) {

            $this->error = '请检查商品编号、规格、购买数量值是否正确';

            return FALSE;
        }

        if (intval($item_id) < 0) {

            $this->error = '商品不存在';

            return FALSE;
        }

        $created = time(); //创建时间

        if (!$newTrade = $this->getOrderData($item_id, $sku_id, $num, $addr['city'])) {

            return FALSE; //商品不允许购买；
        }

        if (!$addr['idcard'] && $newTrade['trade_type'] == TRADE_TYPE_FTZ) {

            $this->error = '必须填写真实的身份证号';
            return FALSE;
        }

        //$total_fee = $pay_amount = $newTrade['_pay_amount']; //实际付款，交易总额  目前是一样的金额

        $pay_amount = $newTrade['_pay_amount']; //实际付款额，
        $discount_fee = $newTrade['_discount_fee']; //优惠金额，
        $logistics_fee = $newTrade['_logistics_fee']; //运费模板运费
        $total_fee = $pay_amount + $discount_fee;    //商品总金额+运费

        if (  $pay_amount < 0 ) {

            $this->error = '无法支付小于0的金额！';

            return FALSE;
        }

        $order = $newTrade['orders']; //取一条购买的商品数据作为交易 主商品 
        //创建交易对象
        $Trades = M('Trades');
        $Trades->startTrans(); //开启事务
        $trades_data = [
            'buyer_user_id' => (int) $_SESSION['BMT_UID'], //会员id 唯一
            'buyer_name' => $_SESSION['BMT_UNAME'], //购买者姓名
            'title' => $order['title'], //交易标题
            'pic_url' => $order['pic_url'], //商品图片
            'price' => $order['_price'], //商品价格
            'num' => $order['num'], //购买数量
            'total_fee' => $total_fee, //商品总金额 商品价格乘以数量
            'pay_amount' => $pay_amount, //付款金额
            'discount_fee' => $discount_fee, //优惠金额
            'created' => $created, //
            'post_fee' => $logistics_fee, //运费  目前 默认是 0
            'status' => WAIT_BUYER_PAY, //交易状态
            'trade_type' => $newTrade['trade_type'], //交易类型
        ];

        $Trades->create($trades_data);
        $trade_id = $Trades->add();   //入库并返回交易ID

        $order_ids = [];

        if ($trade_id) {

            $trade_order = M('TradeOrders');
                      
            //创建 交易订单
          

            $orders_data = [
                'tid' => $trade_id,
                'item_id' => $order['item_id'],
                'title' => $order['title'],
                'pic_url' => $order['pic_url'],
                'price' => $order['_price'],
                'num' => $order['num'],
                'total_fee' => $order['_total_fee'],
                'discount_fee' => $discount_fee,
                'sku_id' => $order['sku_id'],
                'sku_spec_name' => $order['sku_spec_name'], //sku规格名称
                'supplier_id' => $order['supplier_id'], //供货商id
                'outer_id' => $order['sku_outer_id'], //商品编号与供货商对接用
                'status' => WAIT_BUYER_PAY, //待支付
                'order_from' => ORDER_FROM_WEIXIN, //订单来源
                'item_activity_id'=>$order['item_activity_id'],//活动商品id
            ];

            if ($trade_order->create($orders_data)) {
                $order_ids[] = $trade_order->add();   //入库并返回定单ID
            }


            //创建交易对应的收货信息；

            $trade_addr = D('TradeShippingAddr');
            //校验楼据
            $addr['tid']=$trade_id;
            if ($trade_addr->create($addr)) {
                //$trade_addr->tid = $trade_id;
                $addr_id = $trade_addr->add(); //入库
            } else {

                $this->error = $trade_addr->getError();
                return FALSE;
            }
            
            if ($order_ids && $addr_id ) {

                $Trades->commit(); //提交事务
                
               //更新商品库存
                StockModel::setIncs($newTrade['orders']);

                //更新用户的地址簿 
                $useraddrs = D('UserAddrs');
                $user_moblie = $useraddrs->where("mobile=$addr[mobile] AND user_id=" . $_SESSION['BMT_UID'])->find();
                if ($user_moblie) {
                    //更新
                    $useraddrs->user_id = $_SESSION['BMT_UID'];
                    $useraddrs->where("mobile=$addr[mobile] AND user_id=" . $_SESSION['BMT_UID'])->save($addr);
                } else {
                    //插入
                    $useraddrs->create($addr);
                    $useraddrs->user_id = $_SESSION['BMT_UID'];
                    $useraddrs->add();
                }

                return [
                    'tid' => $trade_id, //交易id
                    'pay_amount' =>  bmt_format_money($newTrade['_pay_amount']), //实付金额  格式化后的价格
                    'created' => date("Y-m-d H:i:s", $created),
                        ];
            }
 
            
        }
        
         $Trades->rollback(); //回滚

        $this->error = '订单创建失败';
        return FALSE;
 
    }

    protected function getLogisticsFee($item_id,$send_city='',$num=1){

        $defaultAddrs = M('user_addrs')->where(array('is_default'=>1,'user_id'=>$_SESSION['BMT_UID']))->find();

        if(isset($defaultAddrs['city']) && $defaultAddrs['city']){
            $send_city = $defaultAddrs['city'];
        }

        $itemsModel = new ItemsModel();
        return $itemsModel->getLogisticTemplate($item_id,$send_city,$num);
    }

}
