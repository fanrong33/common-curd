<?php
/**
 * 默认服务器正式环境配置
 */
if (!defined('THINK_PATH')) exit();
$config = array(
	
    'AUTH_SALT'             => 'email_iZiU0CA!24SBVSM7C^qM',         // 盐

    /* cookie设置 */
    'COOKIE_PREFIX'         => '',              // cookie 名称前缀
    'COOKIE_EXPIRE'         => '2592000',       // cookie 保存时间
    'COOKIE_PATH'           => '/',             // cookie 保存路径
    'COOKIE_DOMAIN'         => '',              // cookie 有效域名

	/* 数据库设置 */
    'DB_TYPE'               => 'mysql',     	// 数据库类型
	'DB_HOST'               => '127.0.0.1', 	// 服务器地址
	'DB_NAME'               => '', 	// 数据库名
	'DB_USER'               => '', 	  	// 用户名
	'DB_PWD'                => '',    	// 密码
	'DB_PORT'               => 33066,        	// 端口
	'DB_PREFIX'             => 't_',    		// 数据库表前缀
	'DB_SUFFIX'             => '',          	// 数据库表后缀
	
	/* 数据缓存设置 */
    'DATA_CACHE_TIME'       => 0,      		// 数据缓存有效期 0表示永久缓存
    'DATA_CACHE_COMPRESS'   => false,   	// 数据缓存是否压缩缓存
    'DATA_CACHE_CHECK'      => false,   	// 数据缓存是否校验缓存
    'DATA_CACHE_PREFIX'     => 'cloak',	// 缓存前缀，不使用域名，否则可能被别人破解
    'DATA_CACHE_TYPE'       => 'Memcache',  // 数据缓存类型,支持:File|Db|Apc|Memcache|Shmop|Sqlite|Xcache|Apachenote|Eaccelerator
	

    'LANG_SWITCH_ON'        => false,   // 开启语言包功能
    'DEFAULT_LANG'          => 'en-us',
    'LANG_LIST'             => 'zh-cn,en-us',

	/* Memcache设置 */
    'MEMCACHE_HOST'			=> '127.0.0.1',	// memcache服务的IP地址
    'MEMCACHE_PORT'			=> 11211,		// memcache服务的端口
	
    'PAGE_LISTROWS'			=> 10,
    'PAGE_ROLLPAGE'			=> 5,
    
	/* URL设置 */
    'URL_CASE_INSENSITIVE'  => true,		// 默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'             => 2,       	// URL访问模式，0-普通模式，1-PATHINFO模式，2-REWRITE模式
    'URL_HTML_SUFFIX'       => '.html',  	// URL伪静态后缀设置
    
    // 'URL_ROUTER_ON' => false, //开启路由
    // 'URL_ROUTE_RULES' => array( //路由正则
        //访问地址http://www.xxx.com/index.php/Home/News/add/id/3.html
        //短地址http://www.xxx.com/add_3.html
        //短链接php里 U('/add_3'); html里 {:U('/add_3')}
        // '/^code\/(\w{9})$/'=>'/code/detail?ident=:1',  

                // $_SERVER['PATH_INFO'] = preg_replace('/^\/code\/(\w{9})$/', "/code/detail/ident/\${1}", $_SERVER['PATH_INFO']);


    // ),

    'VAR_PAGE'				=> 'p',
    
    
   	/********* 服务器正式环境配置 *********/
   	
	/* 日志设置 */
    'LOG_RECORD'            => false,   // 默认不记录日志
	/* 模板引擎设置 */
	'TMPL_CACHE_ON'    		=> true,  	// 开启模板编译缓存,设为false则每次都会重新编译
    // 'TMPL_ACTION_SUCCESS'   => TMPL_PATH.'Public/dispatch_jump.tpl', // 默认成功跳转对应的模板文件
    // 'TMPL_ACTION_ERROR'     => TMPL_PATH.'Public/dispatch_jump.tpl', // 默认错误跳转对应的模板文件
    // 'TMPL_EXCEPTION_FILE'   => TMPL_PATH.'Public/think_exception.tpl',// 异常页面的模板文件
	/* 数据库设置 */
    'DB_SQL_LOG'			=> false, 	// 不记录SQL信息
    'DB_FIELDS_CACHE'		=> true, 	// 字段缓存信息
    /* 项目设定 */
    'APP_FILE_CASE'  		=> false, 	// 关闭检查文件的大小写 对Windows平台有效
);

$url_route_rules = include "url_route_rules.php";
return array_merge($config, $url_route_rules);
?>