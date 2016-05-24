<?php
/**
 * @author: peanut
 * @date: 2016-03-31
 * @time: 16:05
 */

//常数文件
include_once('define.php');

// 登录后获取用户id 和name
function userinfo() {
    $uid = session(C('BACKEND_ID'));
    $uname = session(C('BACKEND_NAME'));
    $arr = array(
        'uid' => $uid,
        'uname' => $uname
    );
    return $arr;
}

