<?php
/**
 * 交易处理类
 * @author [chan] <[<maclechan@qq.com>]>
 * @date(2016-04-06)
 */
namespace Api\Controller;

use Common\Model\TradesModel;
use Common\Model\StockModel;

class TradeController extends BaseController
{
    /**订单列表【接口】
     * @param int $page=1 当前页
     * @return array $data 交易信息
     */
    public function trade_list()
    {
        $page = intval(I('get.page',1));
        $trade = new TradesModel();
        $result = $trade->getTrades($page);
        $this->echoJSON($result, $trade);
    }

    /** 订单详情【接口】
     * @param int $tid 交易ID
     * @return array $detail 交易详情
     */
    public function detail()
    {
        $tid = intval(I('get.tid'));
        $trade = new TradesModel();
        $result = $trade->getDetail($tid);
        $this->echoJSON($result);
    }

    /** 取消订单接口
     * @param int $tid 交易ID
     */
    public function cancel()
    {
        //post请求 交易ID
        $tid = intval(I('post.tid'));
        if ($tid>0) {
            //只有交易状态为未付款才能取消交易
            $trade = M("Trades");
            $status = $trade->where(['tid'=>$tid, 'buyer_user_id'=>(int)$_SESSION['BMT_UID']])->getField('status');
            if($status == WAIT_BUYER_PAY){
                $trade->startTrans();//开启事务
                //更新交易状态

                $updatetrade=['status'=>TRADE_CLOSED, 'end_time'=>  time(),'close_mark'=>'用户自己取消了交易'];

                $result = $trade-> where(["tid"=>$tid])->setField($updatetrade);

                $updateOrder=['status'=>TRADE_CLOSED, 'close_mark'=>'用户自己取消了订单'];

                $result2 = M("TradeOrders")->where(["tid"=>$tid])->setField($updateOrder);

                if($result  && $result2 ) {

                    $trade->commit();//提交事务


                    //退还库存

                    $Orders=M("TradeOrders")->where(["tid"=>$tid])->field('item_id,sku_id,num')->select();

                    foreach ($Orders as $order) {

                        StockModel::setInc($order['item_id'], $order['sku_id'], $order['num']);

                    }

                    $this->echoJSON(TRUE);


                }else{

                    $trade->rollback();//回滚
                }

            }

        }

        $this->echoJSON(FALSE,'取消失败');
    }

    /** 用户确认收货接口
     * @param int $tid 交易ID
     */
    public function success()
    {
        //post请求 交易ID
        $tid = intval(I('post.tid'));
        if ($tid>0) {
            //只有交易状态为未付款才能取消交易
            $trade = M("Trades");
            $status = $trade->where(["tid"=>$tid,'buyer_user_id'=>(int)$_SESSION['BMT_UID']])->getField('status');
            if($status == WAIT_BUYER_CONFIRM_GOODS){
                $trade->startTrans();//开启事务
                //更新交易状态
                $updatetrade=['status'=>TRADE_FINISHED, 'end_time'=>  time()];
                $result = $trade-> where(["tid"=>$tid])->setField($updatetrade);

                $result2 = M("TradeOrders")->where(["tid"=>$tid])->setField('status',TRADE_FINISHED);

                if($result  && $result2) {

                    $trade->commit();//提交事务

                    $this->echoJSON(TRUE);

                }

                $trade->rollback();//回滚
            }
        }

        $this->echoJSON(FALSE,'收货确认失败');
    }

    /** 物流数据【接口】
     * @param $invoice_no 物流编号
     * @param $logistics_company 物流公司名称
     * @return json logistics 物流信息
     */
    public function trace_detail()
    {
        $invoice_no = I('get.invoice_no');
        $logistics_company = I('get.logistics_company');
        if($invoice_no  && $logistics_company ) {
            $Logistics = M('TradeLogistics')->where(['invoice_no'=>$invoice_no,'logistics_company'=>$logistics_company])->find();
            if ($Logistics) {
                $response = [
                    'logistics_company' =>C('express_company.'.$logistics_company),
                    'invoice_no' => $Logistics['invoice_no'],
                    'logistics' => json_decode(StripSlashes($Logistics['logistics_data'])),
                ];

                $this->echoJSON($response);

            }else{

                $this->echoJSON(FALSE,'无法找到交易的物流信息');
            }
        } else {

            $this->echoJSON(FALSE,'物流查询参数不能为空');
        }
    }
}
