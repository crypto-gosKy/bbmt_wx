<?php

/**
 * 分类模型
 * @author lihengchen@baobeimt.com
 * @date 2016-4-5
 */

namespace Common\Model;

use Think\Model;

class ItemCatsModel extends Model {

    /**
     * 分类模型
     * @access public
     */
    public function index() {
        $cateObj = M('ItemCats');
        $cateRow = $cateObj->order(array('sort_order' => 'asc'))->select(); //按 sort_order 排序
        $cates = [
            "type_name_1" => "国内行货",
            "cat_list_1" => [],
            "type_name_2" => "保税区直供",
            "cat_list_2" => []
        ];

        foreach ($cateRow as $k => $v) {
            if ($v['type_1'] == 1 && $v['is_parent'] != 0) {
                $cates['cat_list_1'][] = ['cid' => $v['cid'], 'cat_name' => $v['name'], 'brand_list' => []];
            }

            if ($v['type_2'] == 1 && $v['is_parent'] != 0) {
                $cates['cat_list_2'][] = ['cid' => $v['cid'], 'cat_name' => $v['name'], 'brand_list' => []];
            }
        }

        foreach ($cates['cat_list_1'] as &$val) {

            $brand_ids=[];
            foreach ($cateRow as $value) {

                if ($val['cid'] == $value['parent_id']  &&  $value['type_1']==1 ) {

                    $val['sub_cat_list'][] = ['cid' => $value['cid'], 'cat_name' => $value['name']];
                    $brand_ids[]=$value['brand_ids'];//二级分类绑定的品牌；
                }
            }
            
            if ($brand_ids) {
               $val['brand_list']=$this->getbrand($brand_ids);    
            }
            
        }

        foreach ($cates['cat_list_2'] as &$val) {

            $brand_ids=[];
            foreach ($cateRow as $value) {

                if ($val['cid'] == $value['parent_id']  &&  $value['type_2']==1 ) {

                    $val['sub_cat_list'][] = ['cid' => $value['cid'], 'cat_name' => $value['name']];
                     $brand_ids[]=$value['brand_ids'];//二级分类绑定的品牌；
                }
            }
            
             if ($brand_ids) {
               $val['brand_list']=$this->getbrand($brand_ids);    
            }
            
        }

        //热门 1
        $h1 = [];
        $b1 = [];
        $brand_ids=[];
        $hot1 = $cateObj->where("is_hot = 1 and type_1= 1 ")->field('name,cid,brand_ids')->select();
        foreach ($hot1 as $k => $v) {

            $h1[$k]['cat_name'] = $v['name'];
            $h1[$k]['cid'] = $v['cid'];
            $brand_ids[]=$v['brand_ids'];//二级分类绑定的品牌；
        }
         if ($brand_ids) {
               $b1=$this->getbrand($brand_ids);    
         }

        $arr = [];
        $arr['cid'] = 0;
        $arr['cat_name'] = '热门推荐';
        $arr['sub_cat_list'] = $h1;
        $arr['brand_list'] = $b1;
        array_unshift($cates['cat_list_1'], $arr);

        //热门2
        $h2 = [];
        $b2 = [];
        $brand_ids=[];
        $hot2 = $cateObj->where("is_hot = 1 and type_2= 1 ")->field('name,cid,brand_ids')->select();
        foreach ($hot2 as $k => $v) {
             
            $h2[$k]['cat_name'] = $v['name'];
            $h2[$k]['cid'] = $v['cid'];
            $brand_ids[]=$v['brand_ids'];//二级分类绑定的品牌；
        }
        if ($brand_ids) {
               $b2=$this->getbrand($brand_ids);    
         }
        $arr2 = [];

        $arr2['cid'] = 0;
        $arr2['cat_name'] = '热门推荐';
        $arr2['sub_cat_list'] = $h2;
        $arr2['brand_list'] = $b2;
        array_unshift($cates['cat_list_2'], $arr2);
        return $cates;
    }

    /**
     * 返回品牌名
     * @param string $brand_ids
     * @return array
     */
    public function getbrand($brand_ids) {
        if (empty($brand_ids)) {
            return [];
        }
        
        if (!is_array($brand_ids)) {//如果是字符串的（如：22,44,55,67） 
            
            $brand_ids=[$brand_ids];
        }
        
        $brand_id_arr = [];
        foreach ($brand_ids as $brand_ids_) {
            foreach (explode(',', $brand_ids_) as $brand_id) {
                if ( $brand_id > 0 && !in_array($brand_id, $brand_id_arr)) {
                     $brand_id_arr[] = intval($brand_id);
                }
            }
        }
        if ($brand_id_arr) {
            $str = implode(',', $brand_id_arr);
            $m = M('item_brands');
            $res = $m->where(" brand_id in ({$str}) ")->field("name as brand_name ,brand_id,pic_url")->order("sort_order asc")->select();
            if ($res) {
                return $res;
            }
        }
        return [];
    }

}
