<?php

namespace ABS;

class SlogConfig {
    
    // 如果为false，那么php捕获的错误会存放到数据库，如果为true，那么直接php错误直接输出到控制台
    public static $PHP_CONSOLE_DEBUG = true;
    
    const TYPE_PHP      = 1;
    const TYPE_MYSQL    = 2;
    const TYPE_SQL      = 3;
    
    // 用来标识SLOG
    public static $typeMap = array(
        'php'       => self::TYPE_PHP,
        'mysql'     => self::TYPE_MYSQL,
        'sql'       => self::TYPE_SQL,
    );
    
    // 文案
    public static $typeText = array(
        self::TYPE_PHP      => 'php',
        self::TYPE_MYSQL    => 'mysql',
        self::TYPE_SQL      => 'sql',
    );
    
    // 错误级别
    const LEVEL_FATAL   = 1;
    const LEVEL_ERROR   = 2;
    const LEVEL_WARN    = 3;
    const LEVEL_INFO    = 4;
    const LEVEL_DEBUG   = 5;
    const LEVEL_TRACE   = 6;
    
    // 文案
    public static $levelText = array(
        self::LEVEL_FATAL   => 'Fatal',
        self::LEVEL_ERROR   => 'Error',
        self::LEVEL_WARN    => 'Warn',
        self::LEVEL_INFO    => 'Info',
        self::LEVEL_DEBUG   => 'Debug',
        self::LEVEL_TRACE   => 'Trace',
    );
    
    // level颜色
    public static $levelColor = array(
        self::LEVEL_FATAL   => 'red',
        self::LEVEL_ERROR   => 'red',
        self::LEVEL_WARN    => 'orange',
        self::LEVEL_INFO    => 'green',
        self::LEVEL_DEBUG   => 'green',
        self::LEVEL_TRACE   => 'green',
    );
    
}
