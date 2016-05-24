<?php

/*
 * 通用函数
 *
 * 对THINKPHP没有的对应的验证方式的补充；
 *
 */

/**
 * 格式化货币单位为元；
 *
 * 数据库中的货币单位为分；
 *
 * @param int $money
 * @return float
 */
function bmt_format_money($money) {

    return sprintf("%.2f", $money * 0.01);

}



/**
 * 验证电话号码
 *
 * type=mobile  手机号码
 * type=tel  固定电话
 * type= 400  400电话
 *
 * 默认 type = mobile ，如果type 为空 会依次验证匹配 mobile，tel，400
 *
 * @param string $tel
 * @param string $type
 * @return boolean
 */
function is_tel($tel,$type='mobile')
{
    $regxArr = array(
        'mobile'  =>  '/^(\+?86-?)?13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$|14[5,7]{1}[0-9]{8}$|17[0,6,7,8]{1}[0-9]{8}$/',
        'tel' =>  '/^(010|02\d{1}|0[3-9]\d{2})-\d{7,9}(-\d+)?$/',
        '400' =>  '/^400(-\d{3,4}){2}$/',
    );
    if($type && isset($regxArr[$type]))
    {
        return preg_match($regxArr[$type], $tel) ? true:false;
    }
    foreach($regxArr as $regx)
    {
        if(preg_match($regx, $tel ))
        {
            return true;
        }
    }
    return false;
}

/**
 * 身份证号校验
 * @param string $id
 * @return boolean
 */
function is_idcard($id='')
{
    $set = array(7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2);
    $ver = array('1','0','x','9','8','7','6','5','4','3','2');
    $arr = str_split($id);
    $sum = 0;
    for ($i = 0; $i < 17; $i++)
    {
        if (!is_numeric($arr[$i]))
        {
            return false;
        }
        $sum += $arr[$i] * $set[$i];
    }
    $mod = $sum % 11;
    if (strcasecmp($ver[$mod],$arr[17]) != 0)
    {
        return false;
    }
    return true;
}


/**
 * 是否为时间字符串
 *
 * 只允许 2013-11-12 11:30:45 这样的时间字符串
 *
 * @access  public
 * @param   string  $time
 * @return  void
 */
function is_time_str($time) {
    $pattern = '/[\d]{4}-[\d]{1,2}-[\d]{1,2}\s[\d]{1,2}:[\d]{1,2}:[\d]{1,2}/';

    return preg_match($pattern, $time);
}

/*
 当前是否微信浏览器访问
	*/
function is_weixin_browser(){
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    if(strpos($user_agent, 'MicroMessenger') === false){
        return 0;
    }else{
        return 1;
    }
}

/**订单导出Excel
 * @param $expTitle 导出名称
 * @param $expCellName
 * @param $expTableData
 * @throws PHPExcel_Exception
 * @throws PHPExcel_Reader_Exception
 */
function exportExcel($expTitle,$expCellName,$expTableData){
    $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
    $fileName = '订单'.date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
    $cellNum = count($expCellName);
    $dataNum = count($expTableData);
    vendor("PHPExcel.PHPExcel");

    $objPHPExcel = new \PHPExcel();
    $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

    $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
    // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));
    for($i=0;$i<$cellNum;$i++){
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]);
    }
    // Miscellaneous glyphs, UTF-8
    for($i=0;$i<$dataNum;$i++){
        for($j=0;$j<$cellNum;$j++){
            $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
        }
    }

    header('pragma:public');
    header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
    header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
}
