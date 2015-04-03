<?php

/**
 * @brief   php错误和异常处理
 */

require_once ABS_PATH . '/base/config/SlogConfig.class.php';

// 处理捕获的错误
function slog_my_error($type, $msg, $file, $line) {
    
    // 忽略的错误
    if (in_array($type, array(
        E_NOTICE,
        E_USER_NOTICE,
    )) || ($type&error_reporting())==0) {
        return true;
    }
    
    // 允许程序继续运行
    if (in_array($type, array(
        E_WARNING,
        E_CORE_WARNING,
        E_COMPILE_WARNING,
        E_USER_WARNING,
        E_DEPRECATED,
        E_USER_DEPRECATED,
        E_STRICT,
    ))) {
        $str = $msg . ' ' . $file . ':' . $line;
        
        // SLOG中发生的错误不去处理
        if (ABS\SlogConfig::$PHP_CONSOLE_DEBUG) {
            echo 'SLOG WARN: ' . $str . '<br/>';
        } else {
            SLOG('php')->warn($str);
        }
        return true;
    }
    
    // 禁止程序继续运行
    if (in_array($type, array(
        E_ERROR,
        E_PARSE,
        E_USER_ERROR,
        E_COMPILE_ERROR,
        E_CORE_ERROR,
        E_RECOVERABLE_ERROR,
    ))) {
        $str = $msg . ' ' . $file . ':' . $line;
        
        // SLOG中发生的错误不去处理
        if (ABS\SlogConfig::$PHP_CONSOLE_DEBUG) {
            echo 'SLOG ERROR: ' . $str;
            exit;
        } else {
            SLOG('php')->error($str);
            // TODO 跳转到404
            U('Url')->redirect(\ABS\GlobalConfig::PAGE404);
        }
    }
}

// 处理未能捕获的错误
function slog_parse_error() {
    
    $e = error_get_last();
    // 捕获到没有被处理的错误
    if ($e) {
        slog_my_error($e['type'], $e['message'], $e['file'], $e['line']);
    }
}

// 处理异常
function slog_my_exception($e) {
    
    slog_my_error(E_USER_ERROR, $e->getMessage(), $e->getFile(), $e->getLine());
    
}

register_shutdown_function('slog_parse_error');
set_error_handler('slog_my_error', error_reporting());
set_exception_handler('slog_my_exception');

/**
 * @brief   返回一个Slogger对象
 */

function SLOG($typeName) {
    
    require_once __DIR__ . '/core/Slogger.class.php';
    
    return ABS\Slogger::obj($typeName);
}
