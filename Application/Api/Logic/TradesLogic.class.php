<?php
/**
 *  @author [chan] <[<maclechan@qq.com>]>
 *  @date(2016-04-28)
 */
namespace Api\Logic;

use Think\Model;
use Api\Model\UsersModel;
use Common\Model\TradesModel;

class TradesLogic extends Model
{
    /**
     * 订单导出
     * @param $user_id 用户ID
     */
    public function getxlsData($user_id)
    {
        if (!empty($user_id)) {
            //店铺信息
            $user = new UsersModel();
            $store = $user->relation(true)->where("user_id=$user_id")->find();

            //交易信息
            $TradesModel = new TradesModel();
            $trade = $TradesModel->relation(true)->where("buyer_user_id=$user_id")->order('tid DESC')->select();
            //重组导出数据
            $xlsData=[];
            foreach ($trade as $k => $v) {
                $xlsData[$k]['created'] = $v['created'] ?date('Y-m-d H:i:s', $v['created']):'';
                $xlsData[$k]['tid'] = $v['tid'];
                $xlsData[$k]['store_name'] = $store['stores']['name'];
                $xlsData[$k]['username'] = $store['username'];//店员账号
                $xlsData[$k]['master_name'] = $store['stores']['master_name'];
                $xlsData[$k]['mobile'] = $store['mobile'];
                $xlsData[$k]['shippingaddr_name'] = $v['Taddr']['name'];
                $xlsData[$k]['shippingaddr_mobile'] = $v['Taddr']['mobile'];
                //定单表
                foreach ($v['Torder'] as $_k => $_v) {
                    //状态判断
                    switch ($_v['status']) {
                        case 1:
                            $status = '待付款';
                            break;
                        case 4:
                            $status = '待清关';
                            break;
                        case 8:
                            $status = '待发货';
                            break;
                        case 10:
                            $status = '待收货';
                            break;
                        case 100:
                            $status = '订单完成';
                            break;
                        case 0:
                            $status = '已关闭';
                            break;
                        default:
                            $status = '待付款';
                    }
                    $xlsData[$k]['status'] = $status;
                    $xlsData[$k]['title'] = $_v['title'];
                    $xlsData[$k]['sku_spec_name'] = $_v['sku_spec_name'];
                    $xlsData[$k]['num'] = (int)$_v['num'];
                    $xlsData[$k]['pay_amount'] = bmt_format_money($_v['total_fee']);
                    $xlsData[$k]['logistics_company'] = empty($_v['logistics_company'])?'':$_v['logistics_company'];
                    $xlsData[$k]['invoice_no']= empty($_v['invoice_no'])?'':$_v['invoice_no'];
                    $xlsData[$k]['outer_id']= $_v['outer_id'];
                }
            }
        }
        $xlsName = "宝贝码头";
        $xlsCell = array(
            array('created','下单时间'),
            array('tid', '订单编号'),
            array('status', '订单状态'),
            array('store_name', '所属门店'),
            array('username', '店员账号'),
            array('master_name', '店员姓名'),
            array('mobile', '店员电话'),
            array('shippingaddr_name', '消费者姓名'),
            array('shippingaddr_mobile', '消费者电话'),
            array('title','商品名称'),
            array('sku_spec_name','商品规格'),
            array('num','数量'),
            array('pay_amount','实收款¥'),
            array('logistics_company','快递公司'),
            array('invoice_no','运单号'),
            array('outer_id','外部订单号'),
        );
        //数据导出函数
        exportExcel($xlsName,$xlsCell,$xlsData);
    }

    /**
     * 获取交易订单
     * @param int $page=1   当前页
     * @param sql $wheresql 查询条件
     * @return array $data  返回订单列表
     */
    public function getTrades($page=1,$wheresql)
    {
        $user_id = (int)$_SESSION['BMT_UID'];//用户ID
        //分页偏移设置
        $length = 10;
        $start = $page>0 ? (($page - 1) * $length) :1 ;

        //店铺信息
        $user = new UsersModel();
        $store = $user->relation(true)->where("user_id=$user_id")->find();

        //交易订单信息
        $TradesModel = new TradesModel();
        $order = ['bmt_trades.tid' => 'DESC'];
        $data = $TradesModel->relation('Torder')->where("$wheresql buyer_user_id=$user_id")
            ->order($order)
            ->limit($start, $length)
            ->join('left join bmt_trade_shipping_addr AS B on B.tid = bmt_trades.tid')
            ->select();
        $count = $TradesModel->relation('Torder')->where("$wheresql buyer_user_id=$user_id")
            ->order($order)
            ->join('left join bmt_trade_shipping_addr AS B on B.tid = bmt_trades.tid')
            ->count();

        if ($data) {
            $trades = [];
            foreach ($data as $k => $v) {
                $trades[$k]['tid'] = $v['tid'];
                $trades[$k]['store_name'] = $store['stores']['name'];
                $trades[$k]['pay_amount'] = bmt_format_money($v['pay_amount']);
                $trades[$k]['trade_status'] =(int)$v['status'];
                $trades[$k]['created'] = $v['created'] ?date('Y-m-d H:i:s', $v['created']):'';
                $trades[$k]['pay_time'] = $v['pay_time']?date('Y-m-d H:i:s', $v['pay_time']):'';
                $trades[$k]['cc_time'] = $v['cc_time']?date('Y-m-d H:i:s', $v['cc_time']):'';
                $trades[$k]['consign_time'] = $v['consign_time']?date('Y-m-d H:i:s', $v['consign_time']):'';
                $trades[$k]['end_time'] = $v['end_time']?date('Y-m-d H:i:s', $v['end_time']):'';
                $trades[$k]['trade_type'] = (int)$v['trade_type'];
                $trades[$k]['shippingaddr_name'] = $v['name'];
                $trades[$k]['shippingaddr_mobile'] = $v['mobile'];
                //订单信息
                foreach ($v['Torder'] as $_k => $_v) {
                    $trades[$k]['orders'][$_k]['title'] = $_v['title'];
                    $trades[$k]['orders'][$_k]['sku_spec_name'] = $_v['sku_spec_name'];
                    $trades[$k]['orders'][$_k]['price'] = bmt_format_money($_v['price']);
                    $trades[$k]['orders'][$_k]['num'] = (int)$_v['num'];
                    $trades[$k]['orders'][$_k]['total_fee'] = bmt_format_money($_v['total_fee']);
                    $trades[$k]['orders'][$_k]['pic_url'] = $_v['pic_url'];
                    $trades[$k]['orders'][$_k]['logistics_company'] = empty($_v['logistics_company'])?'':$_v['logistics_company'];
                    $trades[$k]['orders'][$_k]['invoice_no']= empty($_v['invoice_no'])?'':$_v['invoice_no'];
                }
            }

            return ['trade_list'=>$trades,'total_page_num'=> ceil($count/$length)];
        }
        $this->error='暂无订单';
        return FALSE;
    }
}