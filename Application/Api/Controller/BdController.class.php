<?php

/**
 * Bd控制器
 * @author lihengchen@baobeimt.com
 * @date 2016-4-19
 */

namespace Api\Controller;

use Api\Model\UserStoresModel;

class BdController extends BaseController {

    public function __construct() {
        if (empty($_SESSION['BMT_BDUSER'])) {
            $this->echoJSON(FALSE, '不是BD用户');
        }
    }

    /*
     * 我的二维码
     */

    public function qr_code() {
        $model = D('Bd');
        $res = $model->getmycode();
        $this->echoJSON($res, D('Bd'));
    }

    /**
     * 我的头像
     */
    public function info() {
        $res = D('Bd')->getwx();
        $this->echoJSON($res, D('Bd'));
    }

    /**
     * 当月业绩
     */
    public function my_kpi() {
        $res = D('user_stores')->mykpi();
        $this->echoJSON($res, D('user_stores'));
    }

    /*     * 我的店铺【接口】
     * @return  shop_name       店铺名
     * @return  month_amount    当月销售
     * @return  month_amount    累计销售
     * @return  total_page_num  总页数
     */

    public function my_shops() {
        $page = intval(I('get.page', 1));
        //分页偏移设置
        $length = 20;
        $start = $page > 0 ? (($page - 1) * $length) : 0;

        $bd_id = (int)$_SESSION['BMT_UID']; //用户ID
        $first_time = mktime(0, 0, 0, date('m'), 1, date('Y')); //当月第一天
        $last_time = mktime(23, 59, 59, date('m'), date('t'), date('Y')); //当月最后一天

        $myshops = new UserStoresModel();
        $trades = $myshops->relation(true)->where("bind_bd_user_id=$bd_id")->limit($start, $length)->select();
        $count = $myshops->relation(true)->where("bind_bd_user_id=$bd_id")->count();
        if ($trades) {
            $return = [];
            foreach ($trades as $k => $v) {
                $return[$k]['shop_name'] = $v['name'];
                $return[$k]['month_amount'] = bmt_format_money(M('trades')->where("(pay_time BETWEEN $first_time AND $last_time) AND status>=4 AND buyer_user_id=" . $v['user_id'])->sum('pay_amount')); //统计当月销售
                $return[$k]['total_amount'] = bmt_format_money(M('trades')->where("status>=4 AND buyer_user_id=" . $v['user_id'])->sum('pay_amount')); //统计累计销售销售
            }
            $this->echoJSON(['stores'=>$return,'total_page_num' => ceil($count / $length)]);
        }
        $this->echoJSON(FALSE, '查询无信息');
    }

}
