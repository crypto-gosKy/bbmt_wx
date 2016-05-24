<?php

/**
 * kpi模型
 * @author lihengchen@baobeimt.com
 * @date 2016-4-19
 */

namespace Api\Model;

use Think\Model\RelationModel;

class UserStoresModel extends RelationModel {

    //关联关系
    protected $_link = [
        //交易定单详情
        'Trades' => [
            'mapping_type' => self::HAS_MANY,
            'class_name' => 'Trades',
            'mapping_name' => 'trades',
            'foreign_key' => 'buyer_user_id',
        ],
    ];

    /**
     * 我的业绩
     */
    public function mykpi() {
        $uid = (int)$_SESSION['BMT_UID'];
        //每月第一天最后一天
        $year = date("Y");
        $month = date("m");
        $allday = date("t");
        $start_time = strtotime($year . "-" . $month . "-1");
        $end_time = strtotime($year . "-" . $month . "-" . $allday.' 23:59:59');

    
        $res = M('user_stores')->where("bind_bd_user_id ={$uid} ")->field('user_id')->select();
      
        $arr = [];
        if (!empty($res)) {
            foreach ($res as $k => $v) {
                $arr[] = $v['user_id'];
            }
        } else {
            return   ['month_amount' => 0, 'total_amount' => 0, 'month_count' => 0, 'total_count' => 0];
        }
        
        //当月开通的店铺

        $month_count = M('user_stores')->where("bind_bd_user_id ={$uid}  and created >={$start_time} and created<={$end_time} ")->count();
        //累计开通的店铺
        $total_count = M('user_stores')->where("bind_bd_user_id ={$uid}")->count();
        
        
        $str = implode(',', $arr);
   
          //当月销售
        $result = M('trades')->where("buyer_user_id in ($str) and pay_time>={$start_time} and pay_time<={$end_time} and status>=4")->field("sum(total_fee) as total_fee")->find();
        //  dump($trade->getLastSql());
        //exit;
        $month_amount = bmt_format_money($result['total_fee']);

        //累计销售
        $result = M('trades')->where("buyer_user_id in ($str)  and status>=4  ")->field('sum(total_fee)  as total_fee ')->find();
        $total_amount = bmt_format_money($result['total_fee']);
       

        return ['month_amount' => ($month_amount), 'total_amount' =>  ($total_amount), 'month_count' => (int) $month_count, 'total_count' => (int) $total_count];

    }

}
