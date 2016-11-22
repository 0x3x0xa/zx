<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  {die('require PHP > 5.3.0 !');} 
// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',true);
define('DS', DIRECTORY_SEPARATOR);//斜杠
define('SITE_DIR', dirname(__FILE__));// 站点目录
define('HTTP_REFERER', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');//来源页面
define('APP_PATH', SITE_DIR . DS . 'App' . DS);// 定义应用目录
define('UPLOAD_PATH', './Public/upload/');//文件上传根目录
define('THINK_PATH', SITE_DIR . DS . 'Libs' . DS . 'ThinkPHP' . DS);
define('RUNTIME_PATH', SITE_DIR . DS . 'Public' . DS . 'Runtime' . DS);// 系统运行时目录

// 引入ThinkPHP入口文件
require(THINK_PATH.'ThinkPHP.php');

// 亲^_^ 后面不需要任何代码了 就是如此简单