<?php
namespace Home\Controller;

use Think\Controller;
use Api\Model\UsersModel;
use Api\Logic\TradesLogic;
use Common\Model\TradesModel;

class IndexController extends \Api\Controller\BaseController
{
    public function index(){
        //$_SESSION['BMT_BDUSER'] // 登录的是BD用户 （默认是 非真 ）
        $name = '这是一大长串的名字';
        $info = '这里是一大长串的内容';
        $this->assign(['name'=>$name,'info'=>$info]);
        $this->display();
    }

    //店铺基本信息
    public function storeinfo()
    {
        $user_id = 1;//$_SESSION['BMT_UID']; //登录的是BD用户
        $myshops = new UsersModel();
        $stores = $myshops->relation(true)->where("user_id=$user_id")->find();
        $this->assign(['stores'=>$stores]);
        $this->display();
    }

    /**
     * 导出全部订单
     */
    public function trade_xls()
    {
       $user_id = (int)$_SESSION['BMT_UID'];
       if (!empty(I('get.flag')) && I('get.flag')==xls) {
           $xlsdata = new TradesLogic();
           $xlsdata->getxlsData($user_id);
       }
    }

    //订单列表
    public function trade_list()
    {
        $page = intval(I('get.page') ?intval(I('get.page')) : 1);
        //----条件查找
        $wheresql = '';
        $param_1 = !empty($_GET['tid'])? ' tid='.intval(trim(I('get.tid'))).' AND':' '; //订单编号
        $param_2 = !empty($_GET['start_time'])?' created>='.strtotime(I('get.start_time')).' AND':''; //下单时间
        $param_3 = !empty($_GET['end_time'])?' created<='.strtotime(I('get.end_time')).' AND':''; //下单时间
        $param_4 = !empty($_GET['status'])?' status='.intval(I('get.status')).' AND':''; //订单状态
        $param_5 = !empty($_GET['goods_name'])?' title like "%'.trim(I('get.goods_name')).'%" AND':''; //商品名称
        $param_6 = !empty($_GET['buyer_name'])?' name like "%'.trim(I('get.buyer_name')).'%" AND':''; //购买者姓名
        $param_7 = !empty($_GET['buyer_mobile'])?' mobile="'.trim(I('get.buyer_mobile')).'" and':''; //收货电话
        //订单状态
        if(I('get.status') == ''){
            $param_4 = '';
        }
        $wheresql = $param_1.$param_2.$param_3.$param_4.$param_5.$param_6.$param_7;

        $trade = new TradesLogic();
        $result = $trade->getTrades($page,$wheresql);
        $this->echoJSON($result, $trade);





        exit;
        $user_id = (int)$_SESSION['BMT_UID'];//用户ID
        $page = intval(I('get.page') ?intval(I('get.page')) : 1);
        //分页偏移设置
        $length = 10;
        $start = $page>0 ? (($page - 1) * $length) :0 ;

        //----条件查找
        $wheresql = '';
        $param_1 = !empty($_GET['orderNum'])? ' tid='.intval(trim(I('get.orderNum'))).' AND':' '; //订单编号
        $param_2 = !empty($_GET['orderTimeStart'])?' created>='.strtotime(I('get.orderTimeStart')).' AND':''; //下单时间
        $param_3 = !empty($_GET['orderTimeEnd'])?' created<='.strtotime(I('get.orderTimeEnd')).' AND':''; //下单时间
        $param_4 = !empty($_GET['orderPayStatus'])?' status='.intval(I('get.orderPayStatus')).' AND':''; //订单状态
        $param_5 = !empty($_GET['goodsName'])?' title like "%'.trim(I('get.goodsName')).'%" AND':''; //商品名称
        $param_6 = !empty($_GET['username'])?' name like "%'.trim(I('get.username')).'%" AND':''; //收货人姓名
        $param_7 = !empty($_GET['usermoblie'])?' mobile="'.trim(I('get.usermoblie')).'" and':''; //收货电话
        //订单全状态
        if(!empty(I('get.orderPayStatus')) && I('get.orderPayStatus')=='all'){
            $param_4 = '';
        }
        $wheresql = $param_1.$param_2.$param_3.$param_4.$param_5.$param_6.$param_7;

        //店铺信息
        $user = new UsersModel();
        $store = $user->relation(true)->where("user_id=$user_id")->find();

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
        //订单导出
        if(!empty(I('get.flag')) && I('get.flag')==excel) {
            $xlsdata = new TradesLogic();
            $xlsdata->getxlsData($user_id);
        }
        $this->error='暂无订单';
        return FALSE;
        //$total_page_num = ceil($count/$length);
        //$trade_list = $trades;
        //$this->assign(['total_page_num'=>$total_page_num, 'trade_list'=>$trade_list]);
        //$this->display();
    }
}