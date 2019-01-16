<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
//绑定模块
//5.0是
define('BIND_MODULE','index');
//5.1是
//Container::get('app')->bind('index')->run()->send();

//define('EXTEND_PATH','../extend/');

error_reporting(E_ALL);
ini_set('display_errors', '1');

//全局变量
require __DIR__ . '/../extend/params.php';

// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';

