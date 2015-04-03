<?php

class UcApi {
    
    public static function checkLogin() {
        
        $password = U('Cookie')->get('cookie01');
        if (empty($password)) {
            return false;
        }
        
        if ($password != self::encryptPassword(PasswordConfig::PASSWORD)) {
            return false;
        }
        return true;
    }
    
    public static function login($password) {
        
        if ($password != self::encryptPassword(PasswordConfig::PASSWORD)) {
            return false;
        }
        $ret = U('Cookie')->set('cookie01', $password);
        return $ret;
    }
    
    public static function encryptPassword($password) {
        
        return md5(sha1($password));
    }
}
