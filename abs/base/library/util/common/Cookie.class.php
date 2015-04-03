<?php

namespace ABS;

class Cookie {
    
    private static $instance = null;
    
    public static function obj() {
        
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private $keys = array();
    
    private function __construct() {
        
        if (empty($this->keys)) {
            $this->keys = GlobalConfig::$cookieKeys;
        }
    }
    
    private function __clone() {}
    
    private function checkKey($key) {
        
        if (!in_array($key, $this->keys)) {
            trigger_error("KEY {$key} 需要在\\ABS\\GlobalConfig中定义！", E_USER_ERROR);
        }
    }
    
    public function set($key, $value, $expire = 0, $path = '/', $domain = '') {
        
        if (empty($domain)) {
            $domain = U('Url')->getLevelDomain('', 1);
            if (!U('Regex')->checkIp($domain)) {
                $domain = '.' . $domain;
            }
        }
        
        $this->checkKey($key);
        return setcookie($key, $value, $expire, $path, $domain);
    }
    
    public function get($key, $default = null, $enableHtml = false) {
        
        $this->checkKey($key);
        if (isset($_COOKIE[$key])) {
            return !$enableHtml ? strip_tags($_COOKIE[$key]) : $_COOKIE[$key];
        }
        return $default;
    }
    
    public function delete($key, $path = '/', $domain = '') {
        
        if (empty($domain)) {
            $domain = U('Url')->getLevelDomain('', 1);
            if (!U('Regex')->checkIp($domain)) {
                $domain = '.' . $domain;
            }
        }
        
        $this->checkKey($key);
        return setcookie($key, '', time()-1, $path, $domain);
    }
}