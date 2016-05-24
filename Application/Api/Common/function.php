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
