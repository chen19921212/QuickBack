<?php

namespace ABS;

/**
 * @brief   ABS框架全局配置
 */

class GlobalConfig {
    
    const PAGE404 = '/404.html';
    
    /**
     * @brief   错误级别定义
     *          如果使用了SLOG获取其他日志服务，该函数只是告诉了系统哪些错误会被写入到日志
     *          ABS_PATH下的bootstrap调用该函数
     */
    public static function initErrorReporting() {
        
        error_reporting(E_ALL & ~E_NOTICE);
    }
    
    // U('Data') 数据文件缓存的路径
    public static function getDataDir() {
        
        // 建议存储到storage，方便做CDN缓存
        return dirname(ABS_PATH) . '/storage';
    }
    
    // U('Data') 文件存储需要申请路径，防止数据覆盖
    public static $dataKeys = array(
        
        'blog/global_count_cache/', // 测试
    );
    
    // U('Cookie') cookie名称申请
    public static $cookieKeys = array(
        
        'cookie01',                 // 存放登录后的密码，加密后的
        'global_framework_notice',  // 页面载入时的tip
    );
    
    // U('Session') session名称申请
    public static $sessionKeys = array(
        
        'www_check_code',   // 默认的，存放验证码的session
    );
    
}
