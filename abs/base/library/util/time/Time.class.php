<?php

namespace ABS;

class Time {
    
    private static $instance = null;
    
    public static function obj() {
        
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {}
    private function __clone() {}
    
    // 结合p方法使用，用来监控时间，并存入$timeList
    public $firstTime  = 0;
    public $recordList = array();
    
    /**
     * @brief 监控时间，将结果存入$record
     */
    public function p($name) {
        
        if (empty($this->firstTime)) {
            $this->firstTime = $this->ms();
            $timeInfo = array(
                'name' => 'Init',
                'time' => date('Y-m-d H:i:s', time()),
            );
            $this->recordList[] = $timeInfo;
        }
        $time = sprintf('%.2lf', $this->ms()-$this->firstTime);
        $timeInfo = array(
            'name' => $name,
            'time' => $time . 'ms',
        );
        $this->recordList[] = $timeInfo;
        return $time;
    }
    
    /**
     * @param   $console    boolean 是否是控制台输出
     */
    public function show($console = false) {
        
        if ($console) {
            foreach ($this->recordList as $timeInfo) {
                echo $timeInfo['name'] . ' At: ' . $timeInfo['time'] . "\n";
            }
        } else {
            foreach ($this->recordList as $timeInfo) {
                echo sprintf('<span style="width: 200px; display: inline-block;">%s</span>', $timeInfo['name'] . ' At: ');
                echo sprintf('<span style="width: 200px; display: inline-block;">%s</span>', $timeInfo['time']);
                echo '<br/>';
            }
        }
    }
    
    /**
     * @brief 返回当前毫秒级时间戳
     */
    public function ms() {
        
        list($usec, $sec) = explode(' ', microtime());
        return sprintf('%.2f', 1000 * ($sec + $usec));
    }
    
    /**
     * @brief 0-6对应的星期，$week也可以是时间戳
     */
    public function getWeekCn($week) {
        
        if (empty($week)) {
            return false;
        }
        $arr = array(
            1   => '星期一',
            2   => '星期二',
            3   => '星期三',
            4   => '星期四',
            5   => '星期五',
            6   => '星期六',
            7   => '星期日',
        );
        $key = $week > 7 ? intval(date('N', $week)) : $week;
        return array_key_exists($key, $arr) ? $arr[$key] : false;
    }
}