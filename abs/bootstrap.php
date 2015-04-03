<?php

if (!defined('ABS_PATH')) {
    define('ABS_PATH', __DIR__);
}

// 载入GlobalConfig
require_once ABS_PATH . '/base/config/GlobalConfig.class.php';
\ABS\GlobalConfig::initErrorReporting();

// 载入公共API
require_once ABS_PATH . '/base/config/PasswordConfig.class.php';
require_once ABS_PATH . '/api/common/UcApi.class.php';
require_once ABS_PATH . '/api/blog/BlogVars.class.php';
require_once ABS_PATH . '/api/blog/ArticleApi.class.php';

// 载入module
require_once ABS_PATH . '/base/module/SLOG/bootstrap.php';
require_once ABS_PATH . '/base/module/TABLE/bootstrap.php';

/**
 * @brief   全局函数F
 */
function F($funcName) {
    
    $file = '';
    
    // 如果没有指定目录，那么从common中找
    if (false === strpos($funcName, '/')) {
        $funcName = 'common/' . $funcName;
    }
    
    $file = ABS_PATH . '/base/library/function/' . $funcName . '.func.php';
    require_once $file;
    
    // 获取可变参数
    $argList = func_get_args();
    $params = array_slice($argList, 1);
    
    
    $arr = explode('/', $funcName);
    $action = $arr[1];
    
    $foo = "ABS\\{$action}";
    return call_user_func_array($foo, $params);
}

/**
 * @brief   全局函数O
 */
function O($class) {
    
    $file = '';
    $className = '';
    
    // 如果没有指定目录，那么先从{$class}文件夹取，没有的话，再从common文件夹取
    if (false === strpos($class, '/')) {
        $className = $class;
        $file = ABS_PATH . '/base/library/object/' . strtolower($className) . '/' . $className . '.class.php';
        if (!is_file($file)) {
            $file = ABS_PATH . '/base/library/object/common/' . $className . '.class.php';
        }
    } else {
        $dir = dirname($class);
        $className = basename($class);
        $className = ucfirst($className);
        $file = ABS_PATH . '/base/library/object/' . $dir . '/' . $className . '.class.php';
    }
    
    require_once $file;
    $className = 'ABS\\' . $className;
    $obj = new $className;
    return $obj;
}

/**
 * @brief   全局函数U
 */
function U($class) {
    
    $file = '';
    $className = '';
    
    // 如果没有指定目录，那么先从{$class}文件夹取，没有的话，再从common文件夹取
    if (false === strpos($class, '/')) {
        $className = $class;
        $file = ABS_PATH . '/base/library/util/' . strtolower($className) . '/' . $className . '.class.php';
        if (!is_file($file)) {
            $file = ABS_PATH . '/base/library/util/common/' . $className . '.class.php';
        }
    } else {
        $dir = dirname($class);
        $className = basename($class);
        $className = ucfirst($className);
        $file = ABS_PATH . '/base/library/util/' . $dir . '/' . $className . '.class.php';
    }
    
    require_once $file;
    $className = 'ABS\\' . $className;
    return $className::obj();
}

