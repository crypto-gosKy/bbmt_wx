<?php

/**
 * 订单【接口】
 * @author [chan] <[<maclechan@qq.com>]>
 * @date(2016-04-08)
 */

namespace Api\Controller;

use Common\Model\TradesModel;
use Common\Model\TradeShippingAddrModel;
use Common\Model\ItemsModel;
use Common\Model\ItemSkusModel;
use Common\Model\StockModel;

class OrderController extends BaseController {
    /* 下单数据 
     * 
     * 步骤如下：
     * 1、判断商品、规格是否存在；
     * 2、商品、规格的库存是否在购买数量内；
     * 
     * @param $item_id 商品id
     * @param $sku_id   sku_id
     * @param $num  购买数量 ,进口商品 这个参数值始终是1
     * @return json 输出数据
     */

    public function item() {
        $item_id = intval(I('get.item_id'));
        $sku_id = intval(I('get.sku_id', 0));

        $num = intval(I('get.num', 1));
        $send_addrs = I('get.send_addrs');

        $TradesModel = new TradesModel();
        $result = $TradesModel->getOrderData($item_id, $sku_id, $num, $send_addrs);
        $this->echoJSON($result, $TradesModel);
    }


    public function logistics_fee(){
        $item_id = I('item_id');
        $city    = trim(I('send_addrs'));
        $num    = trim(I('num'));

        $items_model = new ItemsModel();
        $res = $items_model->getLogisticTemplate($item_id, $city, $num);

        $this->ajaxReturn($res);
    }

    /** 提交订单【接口】
     * 
     */
    public function submit() {
        $item_id = intval(I('item_id', 0));
        $sku_id = intval(I('sku_id', 0));
        $num = intval(I('num', 1)); //购买数量
             
        $addr=['state'=>I('state'),'city'=>I('city'),'district'=>I('district'),'address'=>I('address')
            
            ,'idcard'=>I('idcard'),'name'=>I('name'),'mobile'=>I('mobile')];
    
        $TradesModel = new TradesModel();
        $result = $TradesModel->submitOrder($item_id, $sku_id, $num,$addr);
        $this->echoJSON($result, $TradesModel);
    }

    /** 提交订单【接口】
     * @return int $tid 交易id(订单编号)
     * @return money $pay_amount 实付金额
     * @return datetime $created 订单创建时间
     */
//    public function submit() {
//        //获取SKU及商品记录
//        $item_id = intval(I('item_id',0));
//        $sku_id = intval(I('sku_id', 0));
//        $num = intval(I('num', 1)); //购买数量
//        $mobile = I('mobile');//手机号
//        if ($num < 1) {
//            $num = 1;
//        }
//        $created=  time();//创建时间
//        if ($item_id > 1) {
//            $ItemsModel = new ItemsModel();
//            $goods = $ItemsModel->relation(true)->where("item_id=$item_id")->find();
//            if ($goods) {
//                //商品类型(1国内，2保税)
//                //交易类型 0国内 1保税
//                $type = intval($goods['type']);
//                if ($type == 1) {
//                    $num = intval($num);
//                    $trade_type = TRADE_TYPE_CN; //国内
//                } else if ($type == 2) {
//                    $num = 1; //保税产品购买数量始终为1
//                    $trade_type = TRADE_TYPE_FTZ; //保税区
//                }
//                
//                if(!I('idcard') && $trade_type == TRADE_TYPE_FTZ){
//                     $this->echoJSON(FALSE,'必须填写真实的身份证号');
//                }
//
//                $t = false;
//                if ($goods['Skus'] && $sku_id) {
//                    //判断商品SKU 中是否存在 用户选择的 $sku_id                  
//                    foreach ($goods['Skus'] as $sku) {
//                        if ($sku_id == $sku['sku_id']) {
//                            $t = true;
//                            break;
//                        }
//
//                    }
//                }
//                if (empty($goods['Skus'])) {//商品没有sku 初始化 sku 数组
//                    $sku = ['sku_id' => 0, 'spec_name' => '', 'supplier_id' => 0, 'sku_outer_id' => 0];
//                } elseif ($t === FALSE) {
//                    $this->echoJSON(FALSE,'请选择正确的商品规格');
//                } else {
//                    $goods['price'] = $sku['price']; //sku 存在的情况下 取 sku的价格 
//                    $goods['quantity']=$sku['quantity'];//sku 存在的情况下 取 sku的库存
//                }
//                //判断库存
//                if ($num <= intval($goods['quantity'])) {
//                     $total_fee =  $pay_amount = intval($goods['price']) * $num; //实际付款金额
//                  //  $total_fee = $pay_amount; // 商品总金额
//                    //创建交易对象
//                    $trade = M('Trades');
//                    $trade->startTrans(); //开启事务
//                    $trades_data = [
//                        'buyer_user_id' => (int)$_SESSION['BMT_UID'], //会员id 唯一
//                        'buyer_name' => $_SESSION['BMT_UNAME'], //购买者姓名
//                        'title' => $goods['title'], //交易标题
//                        'pic_url' => $goods['pic_url'], //商品图片
//                        'price' => $goods['price'], //商品价格
//                        'num' => $num, //商品数量
//                        'total_fee' => $total_fee, //商品总金额 商品价格乘以数量
//                        'pay_amount' => $pay_amount, //付款金额
//                        'created' => $created, //
//                        'post_fee' => 0, //运费
//                        'status' => WAIT_BUYER_PAY, //交易状态
//                        'trade_type' => $trade_type, //交易类型
//                    ];
//                    $trade->create($trades_data);
//                    $trade_id = $trade->add();   //入库并返回交易ID
//                    //创建订单对象
//                    if ($trade_id) {
//                        $trade_order = M('TradeOrders');
//                        $orders_data = [
//                            'tid' => $trade_id,
//                            'item_id' => $goods['item_id'],
//                            'title' => $goods['title'],
//                            'pic_url' => $goods['pic_url'],
//                            'price' => $goods['price'],
//                            'num' => $num,
//                            'total_fee' => $total_fee,
//                            'sku_id' => $sku['sku_id'],
//                            'sku_spec_name' => $sku['spec_name'], //sku规格名称
//                            'supplier_id' => $sku['supplier_id'], //供货商id
//                            'outer_id' => $sku['sku_outer_id'], //商品编号与供货商对接用
//                            'status' => WAIT_BUYER_PAY, //待支付
//                            'order_from' => ORDER_FROM_WEIXIN, //订单来源                                   
//                        ];
//                       
//                        if($trade_order->create($orders_data)){
//                              $order_id = $trade_order->add();   //入库并返回定单ID
//                        }
//                        //创建地址对象
//                        $trade_addr = D('TradeShippingAddr');
//                        //校验楼据
//                        if ($addrinfo=$trade_addr->create()) {
//                            $trade_addr->tid = $trade_id;
//                            $addr_id = $trade_addr->add(); //入库
//                        }else{
//                            $this->echoJSON(FALSE,$trade_addr);
//                        }
//                        if ($order_id && $addr_id) {
//                            $trade->commit(); //提交事务
//                             //更新商品库存
//                            StockModel::setDec($item_id, $sku['sku_id'], $num );
//
//                            //更新收货地址
//                            $useraddrs = D('UserAddrs');
//                            $user_moblie = $useraddrs->where("mobile=$mobile AND user_id=". $_SESSION['BMT_UID'])->find();
//                            if($user_moblie){
//                                //更新
//                                $useraddrs->user_id = $_SESSION['BMT_UID'];
//                                $useraddrs->where("mobile=$mobile AND user_id=". $_SESSION['BMT_UID'])->save($addrinfo);
//                            }else{
//                                //插入
//                                $useraddrs->create($addrinfo);
//                                $useraddrs->user_id = $_SESSION['BMT_UID'];
//                                $useraddrs->add();
//                            }
//                            $this->echoJSON([
//                                    'tid' => $trade_id, //交易id
//                                    'pay_amount' => bmt_format_money($pay_amount), //实付金额
//                                    'created' => date("Y-m-d H:i:s", $created),
//                                ],$trade);
//                            
//                        } else {
//                            $trade->rollback(); //回滚
//                            $this->echoJSON(FALSE,'订单创建失败'); 
//                        }
//                    }
//                } else {
//                     $this->echoJSON(FALSE,'库存不足');                   
//                }
//            }
//             $this->echoJSON(FALSE,'购买的商品不存在');  
//        }
//        $this->echoJSON(FALSE,'订单创建失败');
//    }
}