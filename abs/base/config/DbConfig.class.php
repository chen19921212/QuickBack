<?php

namespace ABS;

class DbConfig {
    
    // 开启sql调试的话，生成的SQL语句都会记录到SLOG后台，仅用在开发
    public static $SAVE_SQL = false;
    
    // 主服务器
    public static $WEB_MASTER = array(
        'host'     => '127.0.0.1',
        'username' => 'root',
        'password' => '123',
        'port'     => 3306,
    );
    
    // 从服务器
    public static $WEB_SLAVE = array(
        'host'     => '127.0.0.1',
        'username' => 'root',
        'password' => '123',
        'port'     => 3306,
    );
}
