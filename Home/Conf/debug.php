<?php
/**
 * 本地开发环境配置
 */
if (!defined('THINK_PATH')) exit();
return  array(

	/* 数据库设置 */
	'DB_HOST'               => '127.0.0.1',    // 服务器地址
	'DB_NAME'               => '2016_curd',    // 数据库名
	'DB_USER'               => 'root', 		   // 用户名
	'DB_PWD'                => 'root', 	       // 密码
    'DB_PORT'               => 3306,           // 端口
    
    'DATA_CACHE_TYPE'       => 'File',
    
	/* Memcache设置 */
    'MEMCACHE_HOST'			=> '127.0.0.1',    // memcache服务的IP地址
    'MEMCACHE_PORT'			=> 11211,		   // memcache服务的端口


	/********* 本地开发环境配置 *********/
    'LOG_RECORD'			=>	true, 		// 开启日志记录
    'LOG_EXCEPTION_RECORD'  => 	true,   	// 记录异常信息日志
    'LOG_LEVEL'       		=>  'EMERG,ALERT,CRIT,ERR,WARN,DEBUG',  // 允许记录的日志级别SQL
    'LOG_FILE_SIZE'         =>  52428800,	// 日志文件大小限制，默认2M=2097152
    'DB_SQL_LOG'			=>	true, 		// 记录SQL信息
    'DB_FIELDS_CACHE'		=> 	false, 		// 关闭字段缓存信息
    'APP_FILE_CASE'  		=>  true, 		// 检查文件的大小写 对Windows平台有效
    'TMPL_CACHE_ON'    		=> 	false, 	 	// 关闭模板编译缓存,设为false则每次都会重新编译 TODO!!!
    'TMPL_STRIP_SPACE'      => 	false, 		// 关闭去除模板文件里面的html空格与换行 TODO!!!
    'SHOW_ERROR_MSG'        => 	false,   	// 显示错误信息
    'SHOW_PAGE_TRACE'		=>	false,      // 页面Trace
);
?>