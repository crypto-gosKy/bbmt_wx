<?php

/**
 * 商品库存操作模型
 * 
 * 
 */

namespace Common\Model;

use Common\Model\ItemsModel;

class StockModel {

    private static function init($item_id, $sku_id) {
 
        if (empty($item_id) && empty($sku_id)) {

            return FALSE;
        }

        $ItemsModel = new ItemsModel();
        $item = $ItemsModel->relation(true)->where(["item_id" => $item_id])->find();

        if (empty($item)) {

            return FALSE;
        }

       
        if ($item['Skus']) {
            
            $t = FALSE;
             
            foreach ($item['Skus'] as $sku) {
                if ($sku_id == $sku['sku_id']) {
                    $t = true;
                    break;
                }
            }
             
            return $t;
        }
        
        return TRUE;
        
    }

    public static  function setDec($item_id, $sku_id, $num) {

        if (($t = self::init($item_id, $sku_id))) {

            M()->startTrans();

            if ($t==2) {  //sku_id  有效
                M("Items")->where(["item_id" => $item_id])->setDec('quantity', $num);
                M("ItemSkus")->where(["sku_id" => $sku_id])->setDec('quantity', $num);
            } else {

                M("Items")->where(["item_id" => $item_id])->setDec('quantity', $num);
            }

            M()->commit();

            return TRUE;
        }

        return FALSE;
    }

    public  static function setInc($item_id, $sku_id, $num) {

        if (($t = self::init($item_id, $sku_id))) {

            M()->startTrans();

            if ($t==2) {  //sku_id  有效
                M("Items")->where(["item_id" => $item_id])->setInc('quantity', $num);
                M("ItemSkus")->where(["sku_id" => $sku_id])->setInc('quantity', $num);
            } else {

                M("Items")->where(["item_id" => $item_id])->setInc('quantity', $num);
            }

            M()->commit();

            return TRUE;
        }

        return FALSE;
    }
    
    
    /**
     * 批量更新库存
     * @param array $order  商品数组
     */
    public static function setIncs($order) {
        
        self::setDec($order['item_id'], $order['sku_id'], $order['num']);
        
    }

}
