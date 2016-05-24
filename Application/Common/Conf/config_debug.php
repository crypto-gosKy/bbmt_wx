<?php
/**
 * @author: peanut
 * @date: 2016-03-31
 * @time: 12:48
 */
return array(

    //调试配置
    'LOG_RECORD' => true,  // 进行日志记录
    'LOG_EXCEPTION_RECORD' => true,    // 是否记录异常信息日志
    'LOG_LEVEL' => 'EMERG,ALERT,CRIT,ERR,WARN,NOTIC,INFO,DEBUG,SQL',  // 允许记录的日志级别
    'DB_FIELDS_CACHE' => false, // 字段缓存信息
    'APP_FILE_CASE' => true, // 是否检查文件的大小写 对Windows平台有效
    'TMPL_CACHE_ON' => false,        // 是否开启模板编译缓存,设为false则每次都会重新编译
    'TMPL_STRIP_SPACE' => false,       // 是否去除模板文件里面的html空格与换行
    'SHOW_ERROR_MSG' => true,    // 显示错误信息
    'SHOW_PAGE_TRACE' => true, //显示调式面板

    'testuser'=>[
            
              41=> 'ooliDw7STz1Hbg-QYbUjrRed4pQ0',//巴萨
            
              159=> 'ooliDw83ImMLg6H755vWyy7iRobs',//三水
            
              525=>'ooliDw48QobxI_q9WWoAXzWspWrA',//枫叶（钱海明）

              524=>'ooliDw8GDOItbRIhDlPlu9dIW9dk',//罗 
            
             522=>'ooliDwziCwsjhCpovMWmGCRNMmnM',//江
            
             50 =>'ooliDw0T_D3LNZM6Om78h4_2W0BI',//西瓜
            
           // 52=>'',
          
        ]
);