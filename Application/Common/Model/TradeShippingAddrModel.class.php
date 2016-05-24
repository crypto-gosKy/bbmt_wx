<?php
/**
 *  交易地址
 *  @author [chan] <[<maclechan@qq.com>]>
 *  @date(2016-04-07)
 */

namespace Common\Model;

use Think\Model;

class TradeShippingAddrModel extends Model
{
    //protected $patchValidate = true;//批量验证
    //数据校验
    protected $_validate = [
            ['tid','require','交易编号不能为空'],
            ['name','require','收货人姓名不能为空'],
            ['mobile','require','手机号不能为空'],
            ['mobile','is_tel','手机格式不正确',self::MUST_VALIDATE,'function',self::MODEL_BOTH],
            ['idcard','is_idcard','身份证号码格式不正确',self::VALUE_VALIDATE,'function',self::MODEL_BOTH],
            ['state','require','省不能为空'],
            ['city','require','市不能为空'],
            ['district','require','县/区不能为空'],
            ['address','require','具体地址不能为空'],
    ];
}