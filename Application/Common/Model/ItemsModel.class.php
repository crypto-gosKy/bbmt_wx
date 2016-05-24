<?php

/**
 * 商品模型
 * @author lihengchen@baobeimt.com
 * @date 2016-4-6
 */

namespace Common\Model;

use Think\Model\RelationModel;
use Api\Model\FreightConfigModel;
use Api\Model\LogisticsMasterplateModel;

class ItemsModel extends RelationModel {
    
    //关联SKU关系
    protected $_link = [
        //商品SKU详情
        'ItemSkus' => [
            'mapping_type' => self::HAS_MANY,
            'class_name' => 'ItemSkus',
            'mapping_name' => 'Skus',
            'foreign_key' => 'item_id',
        ],   
    ];

    /**
     * 获取商品详细数据
     * 
     * @param int $item_id
     * @return mix
     */
    public function detail($item_id) {
        
        settype($item_id, 'int');

        if ($item_id<1) {
            $this->error = '商品id不正确';
            return FALSE;
        }
        $res = $this->where("item_id={$item_id} ")
                ->field('item_id,cid,brand_id,title,made_from,desc,intro,buying_tips,price,quantity,market_price,pic_url,type,status,pic_ex_urls,created, item_activity_id')
                ->find();
        if (empty($res)) {
            $this->error = '商品不存在';
            return false;
        }

        //商品附属信息
        $detail = M('item_info')->where(['item_id'=>$item_id])->field('name,value')->select();
        if ($detail) {
            $res['detail'] = $detail;
        }

        //首单立减 单位(元)
        $res['first_order_discount'] = bmt_format_money(TRADE_FIRST);
        
        $res['desc'] = trim($res['desc']);
        $res['intro'] = trim($res['intro']);

        //是活动商品
        if( $res['item_activity_id'] > 0  &&  $Activity=M('ItemActivity')->find($res['item_activity_id']) ){
            
            $res['item_activity_id']=(int)$res['item_activity_id'];
            $res['activity_type']=(int)$Activity['type'];         
            if ($Activity['status']) {  //活动启用
                if($Activity['type']==2){
                    //限时活动的商品是否在活动有效时间内 
                    $res['now_time']=  time();
                    $res['activity_start_time']=(int)$Activity['activity_start_time'];
                    $res['activity_end_time']=(int)$Activity['activity_end_time'];        
                    if($res['now_time'] < $Activity['activity_start_time']){//未开始
                        $res['buy_status'] = 0;
                    }elseif ($res['now_time'] > $Activity['activity_end_time']){//己结束
                        $res['buy_status'] = 2;
                    }else{//可购买
                        $res['buy_status'] = 1;
                    }
                }elseif($Activity['type']==1){//普通活动

                    $res['buy_status'] = 1;
                }
            }else{
                
                $res['buy_status'] = 2;//活动已结束
            }
            //统计参与活动的用户
            $order = D('Trades')
                ->field('bmt_trades.tid,bmt_trades.buyer_user_id,bmt_trades.pay_time,B.oid,C.uid,C.nickname,C.headimgurl')
                ->join('left join bmt_trade_orders AS B on B.tid = bmt_trades.tid')
                ->join('left join bmt_wechat_user AS C on C.bmt_uid = bmt_trades.buyer_user_id')
                ->where("B.item_id=$item_id AND B.item_activity_id=$res[item_activity_id] AND B.status>=4")
                ->order('bmt_trades.tid DESC')
                ->select();
            $res['user_num'] = count($order);
            //获取活动用户信息
            foreach ($order as $k=>$v){
                $res['wechat'][$k]['headimgurl']=$v['headimgurl'];
                $res['wechat'][$k]['nickname']=$v['nickname'];
                $res['wechat'][$k]['pay_time']=date("Y.m.d H:i:s",$v['pay_time']);
            }

        }

        //商品详情图 需要字段  pics-轮播图 放一维数组里 title-标题 price-销售价格 market_price-市场价 商品描述-desc type-类型（单选） 国内产品=1，保税区直供=2 specs-sku信息
        $res['pics'] = explode(',', $res['pic_ex_urls']);
        array_unshift($res['pics'], $res['pic_url']);
        unset($res['pic_ex_urls']);
        $res['quantity'] = (int) $res['quantity'];
        $res['price'] = bmt_format_money($res['price']);
        $res['market_price'] = bmt_format_money($res['market_price']);
        $res['created']=  date('Y-m-d H:i:s', $res['created']);
        $res['type']=(int)$res['type'];
        $res['status']=(int)$res['status'];
        //$res['modified']=  date('Y-m-d H:i:s',  $res['modified']);
        //获取sku信息
        $sku_arr = M('item_skus')->where("item_id={$item_id}")->field("sku_id,spec_name,price")->select();
        foreach ($sku_arr as $k => &$v) {
            $num = intval($v['spec_name']);
            $v['price1'] = bmt_format_money($v['price'] / $num);
            $v['price'] = bmt_format_money($v['price']);
        }
        $res['specs'] = $sku_arr;
        unset($res['spec']);
        //获取所属分类
        $cat = M("item_cats")->where("cid={$res['cid']}")->field('name')->find();
        $res['cat_name'] = $cat['name'];

        //所属品牌
        $brand = M("ItemBrands")->where("brand_id={$res['brand_id']}")->find();
        $res['brand_name'] = $brand['name'];

        return $res;
    }

    /**
     * 商品列表页 模型
     * @access public
     * @param int $cat_id 分类id
     * @param int $brand_id 品牌id
     * @param int $type 商品类型 国内产品=1，保税区直供=2
     * @param string $keyword 关键词
     * @param int $page 当前页
     * @return mix
     * 
     */
    public function getitems($cat_id, $brand_id, $type, $keyword, $page, $item_activity_id) {
       
        $length = 10;
        $start = $page > 0 ? (($page - 1) * $length) : 0;
        $items_model = M('items');
        $where = " 1 ";
        $bind = [];

        //二级分类
        if (intval($cat_id) > 0) {
            $res = M('item_cats')->where(['cid' => $cat_id])->getField('is_parent');
            if ($res['is_parent'] == 1) {
                $result = M('item_cats')->where(['parent_id' => $cat_id])->field('cid')->select();
                $arr = [0];
                if($result){
                    foreach ($result as $k => $v) {
                        $arr[] = $v['cid'];
                    }
                }
                $str = implode(',', $arr);
                $where.=" AND cid in ($str) ";//如果主类下面没有二级分类 就显示 无商品
            } else {
                $where.="  AND cid= :cid  ";
                $bind[':cid'] = [$cat_id, \PDO::PARAM_INT];
            }
        }

        //品牌
        if (intval($brand_id) > 0) {

            $where.=" AND brand_id=:brand_id";
            $bind[':brand_id'] = [$brand_id, \PDO::PARAM_INT];
        }

        //商品类型
        if (intval($type) > 0) {
            $where.=" AND type= :type";
            $bind[':type'] = [$type, \PDO::PARAM_INT];
        }

        //搜索title
        if (!empty($keyword)) {
            $keyword = trim($keyword);
            $where.=" AND title like :title  ";
            $bind[':title'] = ["%$keyword%", \PDO::PARAM_STR];
        }

        //商品是否参与活动
        if (intval($item_activity_id) >0 ) {
            $where.=" AND item_activity_id= :item_activity_id";
            $bind[':item_activity_id']=[$item_activity_id,  \PDO::PARAM_INT];
        }else{
            $where.=" AND item_activity_id= 0 ";
        }

        $where.=" AND  status =1  ";  //必须是上架状态
        $nowtime=  time();
        $res = $items_model->field('item_id,cid,brand_id,title,price,market_price,quantity,pic_url,type,status,created, item_activity_id')->where($where)->bind($bind)->limit($start, $length)->select();
        $count=$items_model->where($where)->bind($bind)->count();
        if (!empty($res)) {
            foreach ($res as $k => &$v) {
                $v['price'] = bmt_format_money($v['price']);
                $v['market_price'] = bmt_format_money($v['market_price']);
                //获取分类名
                $cat_model = M('item_cats');
                $cat_arr = $cat_model->where("cid=:cid")->bind([':cid' => [$v['cid'], \PDO::PARAM_INT]])->field('name')->find();
                $v['cat_name'] = $cat_arr['name'];
                $v['quantity']=(int)$v['quantity'];
                $v['item_id']=(int)$v['item_id'];
                $v['cid']=(int)$v['cid'];
                $v['brand_id']=(int)$v['brand_id'];
                $v['type']=(int)$v['type'];
                $v['status']=(int)$v['status'];
                $v['created']= date('Y-m-d H:i:s',  $v['created']);
                $v['item_activity_id'] = (int)$v['item_activity_id'];
                if( $v['item_activity_id'] > 0  &&  $Activity=M('ItemActivity')->find($v['item_activity_id']) ){
                    
                  if ($Activity['status']) {//活动启用
                    if($Activity['type']==2){ //限时活动的商品是否在活动有效时间内 
                        $v['now_time']=$nowtime;
                        $v['activity_start_time']=(int)$Activity['activity_start_time'];
                        $v['activity_end_time']= (int)$Activity['activity_end_time'];
                        $v['activity_type']= (int)$Activity['type'];
                        if($v['now_time'] < $Activity['activity_start_time']){//未开始
                            $v['buy_status'] = 0;
                        }elseif ($v['now_time'] > $Activity['activity_end_time']){//己结束
                            $v['buy_status'] = 2;
                        }else{//可购买
                            $v['buy_status'] = 1;
                        }
                    }elseif ($Activity['type']==1) {//普通活动
                        $v['buy_status'] = 1;
                    }
                     }else{
                     $v['buy_status'] = 2;//己结束
                    }
                    $v['activity_user_num']  = D('Trades')
                    ->join('left join bmt_trade_orders AS B on B.tid = bmt_trades.tid')
                    ->where("B.item_id=$v[item_id] AND B.item_activity_id=$v[item_activity_id] AND B.status>=4")
                    ->count(); 
                }
            }
            return ['item_list' => $res, 'total_page_num' => ceil($count / $length), 'total_count_num' => (int) $count];
        }
        if ($item_activity_id) {
             $this->error = 'Sorry！活动已结束';
        }else{
             $this->error = '无商品数据';
        }
        return FALSE;
    }

    /**
     * 获取商品库存
     * @param type $item_id
     * @param type $sku_id
     * @return mix
     * {
      "item_id": 52,
      "sku_id": 248,
      "quantity": 443
      }
     */
    public function getItemQuantity($item_id = 0, $sku_id = 0) {

        settype($item_id, 'int');
        settype($sku_id, 'int');
        if ($item_id < 1 && $sku_id < 1) {
            $this->error = '参数错误';
            return FALSE;
        }
        if ($sku_id > 0) {  //查询 sku的库存
            $data = M("item_skus")->where(['sku_id' => $sku_id])->field('quantity,item_id')->find();
            if ($data && $data['item_id'] == $item_id) {
                return ['item_id' => $item_id, 'sku_id' => $sku_id, 'quantity' => (int) $data['quantity']];
            }
        } else {  //查询item的库存
            $data = M("items")->where(['item_id' => $item_id])->field('quantity')->find();

            if ($data) {
                return ['item_id' => $item_id, 'sku_id' => $sku_id, 'quantity' => (int) $data['quantity']];
            }
        }
        $this->error = '商品记录不存在';
        return false;
    }

    /**
     * 根据运费模板计算运费
     * @param $item_id
     * @param $distribution_city
     * @param $num
     * @return array
     */
    public function getLogisticTemplate($item_id,$distribution_city='',$num=1){
        /**===================================*
         * 考虑到三张表，放弃使用连表查询
         * ===================================*/
        $logistics_fee = 0;
        if(!$item_id){
            return array('return_code'=>1,'return_msg'=>'缺少商品id');
        }
        //获取商品信息
        $item_where = array(
            'item_id'     => $item_id,
            'is_delete'   => 0,
            'status'      => 1
        );
        $item_info = $this->where($item_where)->find();
        if(!$item_info){
            return array('return_code'=>1,'return_msg'=>'该商品已下架或删除');
        }
        //通过运单模板id找到所有配置信息
        if($item_info['logistics_masterplate_id'] == 0){        //商家包邮模板,直接返回
            return array('return_code'=>0,'data'=>array('logistics_fee'=>0.00));
        }
        //查找模板信息
        $template_where = array(
            'id' => $item_info['logistics_masterplate_id']
        );
        $templateModel = new LogisticsMasterplateModel();
        $template_info  = $templateModel->where($template_where)->find();
        if(!$template_info){
            return array('return_code'=>1,'return_msg'=>'该运费模板已下架或删除');
        }
        $map = array(
            'logistics_masterplate_id' => $item_info['logistics_masterplate_id']
        );
        $templateConfigModel = new FreightConfigModel();
        $config_info = $templateConfigModel->where($map)->select();
        $default_config = array();
        $useful_config = array();

        if($config_info){
            foreach($config_info as $key => $value){
                if($value['is_default'] == 1){  //默认配置，全国
                    $default_config = $value;
                }else{
                    if($distribution_city && $value['distribution_city']){
                        $city_arr = explode(',',$value['distribution_city']);
                        if(in_array($distribution_city, $city_arr)){
                            $useful_config = $value;
                            break;
                        }
                    }
                }
            }

            if(empty($useful_config)){
                $useful_config = $default_config;
            }
            //计算金额
            $logistics_fee = $this->valuation_logistics_fee($template_info['valuation_method'],$item_info,$useful_config,$num);
        }
        $logistics_fee = bmt_format_money($logistics_fee);
        return array('return_code'=>0,'data'=>array('logistics_fee'=>$logistics_fee));
    }

    /**
     * 计算运费算法
     * @param $method
     * @param $item_info
     * @param $config
     * @param $num
     * @return int
     */
    protected function valuation_logistics_fee($method,$item_info,$config,$num){
        $_fee = 0;
        switch($method){
            case 'count':
                if($num <= $config['first_scope']){
                    $_fee = $config['first_price'];
                }else{
                    $times = $num - $config['first_scope'];
                    $_fee  = $config['first_price'] + ($times * $config['increment_price']);
                }
                break;
            case 'weight':
                $item_info['logistics_weight'] = $num * $item_info['logistics_weight'];
                if($item_info['logistics_weight'] <= $config['first_scope']){
                    $_fee = $config['first_price'];
                }else{
                    $times  = ceil(($item_info['logistics_weight'] - $config['first_scope']) / $config['increment']);
//                    $other_fee = ($item_info['logistics_weight'] - ($times * $config['first_scope'])) * $config['increment_price'];
                    $_fee = $config['first_price'] + ($times * $config['increment_price']);
                }
                break;
            case 'volume':
                $item_info['logistics_volume'] = $num * $item_info['logistics_volume'];
                if($item_info['logistics_volume'] <= $config['first_scope']){
                    $_fee = $config['first_price'];
                }else{
                    $times  = ceil(($item_info['logistics_volume'] - $config['first_scope']) / $config['increment']);
//                    $other_fee = ($item_info['logistics_volume'] - ($times * $config['first_scope'])) * $config['increment_price'];
                    $_fee = $config['first_price'] + ($times * $config['increment_price']);
                }
                break;
            default:
                $_fee = 0;
                break;
        }
        return $_fee;
    }

}
