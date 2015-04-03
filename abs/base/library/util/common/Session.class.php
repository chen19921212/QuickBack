<?php

namespace ABS;

class Session {
    
    private static $instance = null;
    
    public static function obj() {
        
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        
        if (empty($this->keys)) {
            $this->keys = GlobalConfig::$sessionKeys;
        }
    }
    
    private function __clone() {}
    
    private $initFinish = false;
    private $keys = array();
    
    private function init() {
        
        if (false === $this->initFinish) {
            $this->initFinish = true;
            session_start();
        }
    }
    
    private function checkKey($key) {
        
        if (!in_array($key, $this->keys)) {
            trigger_error("KEY {$key} 没有定义！", E_USER_ERROR);
        }
    }
    
    public function set($key, $value) {
        
        $this->checkKey($key);
        $this->init();
        $_SESSION[$key] = $value;
        return true;
    }
    
    public function get($key, $default = null) {
        
        $this->checkKey($key);
        $this->init();
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return $default;
    }
    
    public function getSessionId() {
        
        return session_id();
    }
    
    public function delete($key) {
        
        $this->checkKey($key);
        $this->init();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    public function clean() {
        
        $this->init();
        session_unset();
        session_destroy();
    }
}