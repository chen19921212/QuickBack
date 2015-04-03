<?php

namespace ABS;

require_once ABS_PATH . '/base/config/DbConfig.class.php';

class Slogger {
    
    // 缓存实例
    private static $instanceList = array();
    
    /**
     * @brief   获取实例
     */
    public static function obj($key) {
        
        if (!array_key_exists($key, self::$instanceList) || empty(self::$instanceList[$key])) {
            self::$instanceList[$key] = new self($key);
        }
        return self::$instanceList[$key];
    }
    
    private $type = 0;
    
    /**
     * @brief   创建一个BucketExt对象
     */
    private function __construct($key) {
        
        $this->type = SlogConfig::$typeMap[$key];
    }
    
    // 防止克隆
    private function __clone() {}
    
    public function error($msg) {
        
        return $this->insert(SlogConfig::LEVEL_ERROR, $msg);
    }
    
    public function warn($msg) {
        
        return $this->insert(SlogConfig::LEVEL_WARN, $msg);
    }
    
    public function info($msg) {
        
        return $this->insert(SlogConfig::LEVEL_INFO, $msg);
    }
    
    private function insert($level, $msg) {
        
        $arr = array_merge($_GET, $_POST, $_REQUEST);
        $set = array();
        foreach ($arr as $key => $value) {
            $value = (string) $value;
            $value = mb_substr($value, 0, 50, 'utf8');
            $set[] = "{$key}={$value}";
        }
        $request = implode(', ', $set);
        
        $traceList = debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
        $traceInfo = $traceList[1];
        $loc = $traceInfo['file'] . ':' . $traceInfo['line'];
        
        $pid = 0;
        if (function_exists('posix_getpid')) {
            $pid = (int) posix_getpid();
        }
        
        $data = array(
            'type'          => $this->type,
            'create_time'   => time(),
            'level'         => $level,
            'pid'           => $pid,
            'remote_ip'     => U('Http')->getIp(),
            'remote_port'   => (int) $_SERVER['REMOTE_PORT'],
            'loc'           => $loc,
            'url'           => U('Url')->getCurrentUrl(),
            'request'       => $request,
            'message'       => $msg,
        );
        
        // 单独SQL插入，不使用Model
        $sqlBuilder = TABLE('log_list')->getSqlBuilder();
        $sql = $sqlBuilder->createInsertSql($data);
        $mysqliExt = TABLE('log_list')->getMysqliExt(true);
        $ret = $mysqliExt->query($sql, false);
        if (false === $ret) {
            trigger_error($mysqliExt->getMysqli()->error, E_USER_ERROR);
        }
        
        return $ret;
    }
}
