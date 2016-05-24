<?php
/**
 * @author: peanut
 * @date: 2016-03-31
 * @time: 16:22
 */
return array(

    //解析sql缓存
    'DB_SQL_BUILD_CACHE' => true,
    //缓存队列长度
    'DB_SQL_BUILD_LENGTH' => 20,

    //静态缓存
    'HTML_CACHE_ON'     =>    true, // 开启静态缓存
    'HTML_CACHE_TIME'   =>    60,   // 全局静态缓存有效期（秒）
    'HTML_FILE_SUFFIX'  =>    '.shtml', // 设置静态缓存文件后缀
);