<?php

/**
 * @brief   对mysqli扩展进行了封装，而且实现单例
 *          MysqliExt类的作用主要有2个：
 *          1. 将一些复杂的代码从BaseModel中分离处理，使BaseModel的逻辑清晰
 *          2. 保证了同一个库下面的Model实例都调用同一个mysqli连接，所以支持同一个库下的事务操作
 */

namespace ABS;

require_once ABS_PATH . '/base/config/DbConfig.class.php';

class MysqliExt {
    
    // 单例模式
    private static $instanceList = array();
    
    /**
     * @brief 获取实例的方法
     */
    public static function obj($server, $dbName, $charset = 'utf8') {
        
        $server['db_name'] = $dbName;
        ksort($server);
        $key = md5(implode('_' , $server));
        
        if (!array_key_exists($key, self::$instanceList) || empty(self::$instanceList[$key])) {
            self::$instanceList[$key] = new self($server, $dbName, $charset);
        }
        
        return self::$instanceList[$key];
    }
    
    // mysqli临时连接
    private $mysqli = null;
    
    // 判断当前是否处在事务状态
    private $inTrans = false;
    
    /**
     * @brief 创建一个mysqli对象
     */
    private function __construct($server, $dbName, $charset) {
        
        // 创建mysqli对象
        $mysqli = @new \mysqli($server['host'], $server['username'], $server['password'], $dbName, $server['port']);
        for ($i = 1; ($i < 3) && $mysqli->connect_error; $i++) {
            usleep(50000);
            $mysqli = @new \mysqli($server['host'], $server['username'], $server['password'], $dbName, $server['port']);
        }
        
        // 连接失败，记录日志
        if ($mysqli->connect_error) {
            trigger_error($mysqli->connect_error, E_USER_ERROR);
            return false;
        }
        
        // 设置字符编码为utf8
        $ret = $mysqli->set_charset($charset);
        if (false === $ret) {
            trigger_error($mysqli->error, E_USER_ERROR);
            return false;
        }
        $this->mysqli = $mysqli;
    }
    
    /**
     * @brief 返回mysqli对象，以供外部使用
     */
    public function getMysqli() {
        
        return $this->mysqli;
    }
    
    // 防止克隆
    private function __clone() {}
    
    /**
     * @brief   插入操作
     * @return  插入成功返回true|insert_id，失败返回false
     */
    public function insert($sql) {
        
        $ret = $this->execute($sql);
        if (false === $ret) {
            return false;
        }
        return $this->mysqli->insert_id > 0 ? $this->mysqli->insert_id : true;
    }
    
    /**
     * @brief   更新操作
     * @return  更新成功返回true|insert_id，失败返回false
     */
    public function update($sql) {
        
        $ret = $this->execute($sql);
        if (false === $ret) {
            return false;
        }
        return $this->mysqli->affected_rows > 0 ? $this->mysqli->affected_rows : true;
    }
    
    /**
     * @brief   普通执行sql操作
     * @return  true | false
     */
    public function execute($sql) {
        
        $ret = $this->query($sql);
        if ($ret === false) {
            $msg = $this->mysqli->error . ' ' . __FILE__ . ':' . __LINE__;
            SLOG('mysql')->error($msg);
            return false;
        }
        return $ret;
    }
    
    /**
     * @brief   query，统一执行sql的方法
     */
    public function query($sql, $saveSql = true) {
        
        if ($saveSql && DbConfig::$SAVE_SQL) {
            SLOG('sql')->info($sql);
        }
        return $this->mysqli->query($sql);
    }
    
    /**
     * @brief   执行查询语句，结果集解析成二维数组
     * @return  false | array()
     */
    public function queryList($sql) {
        
        $result = $this->query($sql);
        if (false === $result) {
            $msg = $this->mysqli->error . ' ' . __FILE__ . ':' . __LINE__;
            SLOG('mysql')->warn($msg);
            return false;
        }
        
        $list = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        return $list;
    }
    
    /**
     * @brief   执行查询语句，获取结果集中的第一条记录
     * @return  false | array()
     */
    public function queryRow($sql) {
        
        $result = $this->query($sql);
        if (false === $result) {
            $msg = $this->mysqli->error . ' ' . __FILE__ . ':' . __LINE__;
            SLOG('mysql')->warn($msg);
            return false;
        }
        
        $row = $result->fetch_assoc();
        $result->free();
        return $row;
    }
    
    /**
     * @brief   开启一个事务
     */
    public function begin() {
        
        if ($this->inTrans) {
            $msg = '上一个事务还没有结束！ ' . __FILE__ . ':' . __LINE__;
            trigger_error($msg, E_USER_ERROR);
        }
        $ret = $this->mysqli->begin_transaction();
        if (false === $ret) {
            $msg = $this->mysqli->error . ' ' . __FILE__ . ':' . __LINE__;
            trigger_error($msg, E_USER_ERROR);
        }
        $this->inTrans = true;
        return true;
    }
    
    /**
     * @brief   提交当前事务
     */
    public function commit() {
        
        if (! $this->inTrans) {
            $msg = '当前没有事务！ ' . __FILE__ . ':' . __LINE__;
            trigger_error($msg, E_USER_ERROR);
        }
        $ret = $this->mysqli->commit();
        if (false === $ret) {
            $msg = $this->mysqli->error . ' ' . __FILE__ . ':' . __LINE__;
            trigger_error($msg, E_USER_ERROR);
        }
        $this->inTrans = false;
        return true;
    }
    
    /**
     * @brief   回滚一个事务
     */
    public function rollback() {
        
        if (! $this->inTrans) {
            $msg = '当前没有事务！ ' . __FILE__ . ':' . __LINE__;
            trigger_error($msg, E_USER_ERROR);
        }
        $ret = $this->mysqli->rollback();
        if (false === $ret) {
            $msg = $this->mysqli->error . ' ' . __FILE__ . ':' . __LINE__;
            trigger_error($msg, E_USER_ERROR);
        }
        $this->inTrans = false;
        return true;
    }

    /**
     * @brief   判断当前事务状态
     */
    public function inTrans() {
        
        return $this->inTrans;
    }
}
