<?php
/**
 *  快递物流信息模型
 *  @author [chan] <[<maclechan@qq.com>]>
 *  @date(2016-04-13)
 */
namespace Common\Model;

use Think\Model;

class TradeLogisticsModel extends Model
{
    protected $patchValidate = true;//批量验证
    //数据校验
    protected $_validate = [
        ['invoice_no','require','快递运单号不能为空'],
      //  ['invoice_no','','快递运单号已经存在',self::MUST_VALIDATE,'unique',self::MODEL_BOTH],
        ['logistics_company','require','快递公司不能为空'],
    ];
}