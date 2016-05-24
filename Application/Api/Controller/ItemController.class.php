<?php

/**
 * 商品接口控制器
 * @author lihengchen@baobeimt.com
 * @date 2016-4-7
 */

namespace Api\Controller;
use Common\Model\ItemCatsModel;
use Common\Model\ItemsModel;

class ItemController extends BaseController {

    /**
     * 获取商品库存
     * @access public
     * @param int item_id 商品ID
     * @param int sku_id 可选
     */
    public function quantity() {
        $item_id = I('item_id', 0, 'int');
        $sku_id = I('sku_id', 0, 'int');
        $ItemsModel = new ItemsModel();
        $result = $ItemsModel->getItemQuantity($item_id, $sku_id);
        $this->echoJSON($result, $ItemsModel);
    }

    /**
     * 商品详情页
     * @access public
     * @param int $item_id 商品ID
     */
    public function detail() {
        //实例化模型
        $item_model = new ItemsModel();
        $result = $item_model->detail(I('item_id',0));
        $this->echoJSON($result, $item_model);
      
    }

    /**
     * 商品列表页控制器
     * @access public
     * @param int $cat_id 分类id
     * @param int $brand_id 品牌id
     * @param int $type 商品类型 国内产品=1，保税区直供=2
     * @param string $keyword 关键词
     * @param int $page 当前页
     */
    public function item_list() {
        //实例化模型
        $item_model = new ItemsModel();
        $result = $item_model->getitems(I('cat_id',0), I('brand_id',0), I('type',0), I('keyword',''), I('page',1), I('item_activity_id',0));
        $this->echoJSON($result, $item_model);
    }

    /*
     * 商品分类控制器
     * @accsee public
     */

    public function cats() {
        //实例化分页类模型
        $cats = new ItemCatsModel();
        $result = $cats->index();
        $this->echoJSON($result, $cats);
    }
    
    

}
