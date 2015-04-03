<?php

namespace ABS;

class Number {
    
    private static $instance = null;
    
    public static function obj() {
        
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {}
    private function __clone() {}
    
    public function isInt($n) {
        
        return (is_numeric($n) && intval($n) == $n);
    }
}
