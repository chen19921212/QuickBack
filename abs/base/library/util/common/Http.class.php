<?php

namespace ABS;

class Http {
    
    private static $instance = null;
    
    public static function obj() {
        
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {}
    private function __clone() {}
    
    // mimeTypes类型，从文件导入
    protected $mimeTypes = array();
    
    public function __get($name) {
        
        if ($name == 'mimeTypes' && empty($this->mimeTypes)) {
            $this->mimeTypes = include __DIR__ . '/mimeTypes.php';
        }
        return $this->$name;
    }
    
    public function isAjax() {
        
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
    }
    
    public function isPost() {
        
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }
    
    public function isGet() {
        
        return $_SERVER['REQUEST_METHOD'] == 'GET';
    }
    
    public function getGET($key, $default = null, $enableHtml = false) {
        
        if (array_key_exists($key, $_GET)) {
            return !$enableHtml ? strip_tags($_GET[$key]) : $_GET[$key];
        }
        return $default;
    }
    
    public function getPOST($key, $default = null, $enableHtml = false) {
        
        if (array_key_exists($key, $_POST)) {
            return !$enableHtml ? strip_tags($_POST[$key]) : $_POST[$key];
        }
        return $default;
    }
    
    public function output($content, $contentType = 'html', $charset = 'utf-8') {
        
        if (empty($this->mimeTypes)) {
            $this->mimeTypes = include __DIR__ . '/mimeTypes.php';
        }
        header('Content-Type: ' . $this->mimeTypes[$contentType] . '; charset=' . $charset);
        if ($contentType == 'json') {
            echo json_encode($content);
        } else {
            echo $content;
        }
    }
    
    /**
     * @brief   作用和output类似，不过这里是以下载的形式输出
     */
    public function download($content, $saveAs = '') {
        
        header('Content-type: application/octet-stream');
        if (!empty($saveAs)) {
            header('Content-Disposition: attachment; filename=' . $saveAs);
        }
        echo $content;
    }
    
    public function ip2long($ip) {
        
        return sprintf('%u', ip2long($ip));
    }
    
    public function long2ip($long) {
        
        return long2ip($long);
    }
    
    /**
     * @brief  获取当前ip，注意返回值是个长整型，可以用long2ip转换
     * @return long int
     */
    public function getIp() {
        
        $ret = 0;
        $ip = getenv('HTTP_CLIENT_IP');
        if($ip && strcasecmp($ip, 'unknown') && !preg_match('/192\.168\.\d+\.\d+/', $ip)) {
            $ret = $this->ip2long($ip);
        }
        $ip = getenv('HTTP_X_FORWARDED_FOR');
        if($ip && strcasecmp($ip, 'unknown')) {
            $ret = $this->ip2long($ip);
        }
        $ip = getenv('REMOTE_ADDR');
        if($ip && strcasecmp($ip, 'unknown')) {
            $ret = $this->ip2long($ip);
        }
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
            if($ip && strcasecmp($ip, 'unknown')) {
                $ret = $this->ip2long($ip);
            }
        }
        return empty($ret) ? 0 : $ret;
    }
}