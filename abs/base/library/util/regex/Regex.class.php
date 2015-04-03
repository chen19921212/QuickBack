<?php

namespace ABS;

class Regex {
    
    private static $instance = null;
    
    public static function obj() {
        
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {}
    private function __clone() {}
    
    const REGEX_USERNAME = '/^[A-Za-z_][A-Za-z0-9_]{3,15}$/';
    const REGEX_PHONE    = '/^1\d{10}$/';
    
    public static function checkUsername($username) {
        
        if (!preg_match(self::REGEX_USERNAME, $username)) {
            return false;
        }
        return true;
    }
    
    public static function checkPassword($password) {
        
        if (strlen($password) < 6 || strlen($password) > 20) {
            return false;
        }
        return true;
    }
    
    public static function checkPhone($phone) {
        
        if (!preg_match(self::REGEX_PHONE, $phone)) {
            return false;
        }
        return true;
    }
    
    public static function checkEmail($email) {
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }
    
    public static function checkIp($ip) {
        
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }
        return true;
    }
}
