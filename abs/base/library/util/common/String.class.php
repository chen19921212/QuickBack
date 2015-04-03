<?php

namespace ABS;

class String {
    
    private static $instance = null;
    
    public static function obj() {
        
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {}
    private function __clone() {}
    
    public function substr_utf8($str, $start, $len) {
        
        return mb_substr($str, $start, $len, 'utf8');
    }
    
    public function sublen_utf8($str) {
        
        return mb_strlen($str, 'utf8');
    }
    
}
