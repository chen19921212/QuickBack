<?php

/**
 * @brief   注意使用情景，需要考虑分布式下的缓存，请使用OCS
 */

namespace ABS;

class Data {
    
    private static $instance = null;
    
    public static function obj() {
        
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private $dir    = array();
    private $keys   = array();
    
    private function __construct() {
        if (empty($this->keys)) {
            $this->dir  = GlobalConfig::getDataDir();
            $this->keys = GlobalConfig::$dataKeys;
        }
    }
    
    private function __clone() {}
    
    private function getPath($fileName) {
        
        $dir = $this->dir . '/' . dirname($fileName);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $this->dir . '/' . $fileName;
    }
    
    private function checkFile($fileName) {
        
        $find = false;
        foreach ($this->keys as $dir) {
            if (0 === strpos($fileName, $dir)) {
                $find = true;
                break;
            }
        }
        if (!$find) {
            trigger_error("FILE {$fileName} 没有在\\ABS\\GlobalConfig中定义！", E_USER_ERROR);
        }
    }
    
    /**
     * @brief   设置文件缓存
     * @param   $path       文件名
     * @param   $expire     过期时间，单位为秒，默认是10天
     */
    public function set($fileName, $value, $expire = 0, $gz = true) {
        
        $this->checkFile($fileName);
        
        if (empty($expire)) {
            $expire = time() + 864000;
        }
        
        // 对$value进行序列化和压缩
        if ($gz) {
            $value = serialize($value);
            $value = gzcompress($value, 3);
        }
        
        // 设置12位过期时间
        $expirePart = sprintf('%012d', $expire);
        
        // 设置32位md5校验码
        $checkPart = md5($value);
        
        // 压缩选项
        $gzPart = $gz ? '1' : '0';
        
        $data = $expirePart . $checkPart . $gzPart . $value;
        
        $path = $this->getPath($fileName);
        $ret = file_put_contents($path, $data);
        return $ret;
    }
    
    /**
     * @brief   允许对过期的文件添加内容
     */
    public function append($fileName, $appendValue, $newExpire = 0, $newGz = true) {
        
        $this->checkFile($fileName);
        
        // 获取文件流
        $path = $this->getPath($fileName);
        if (!is_file($path)) {
            return $this->set($fileName, $appendValue, $newExpire, $newGz);
        }
        $data = file_get_contents($path);
        if (false === $data) {
            return false;
        }
        
        // 获取时间
        $expire = (int) substr($data, 0, 12);
        
        // 文件校验
        $check = substr($data, 12, 32);
        
        // 压缩选项
        $gz = substr($data, 44, 1);
        
        // md5校验
        $value = substr($data, 45);
        if ($check != md5($value)) {
            return false;
        }
        
        // 解压，反序列化
        if ($gz) {
            $value = gzuncompress($value);
            $value = unserialize($value);
        }
        
        $expire = empty($newExpire) ? $expire : $newExpire;
        $value .= $appendValue;
        return $this->set($fileName, $value, $expire, $newGz);
    }
    
    public function get($fileName) {
        
        $this->checkFile($fileName);
        
        // 获取文件流
        $path = $this->getPath($fileName);
        if (!is_file($path)) {
            return false;
        }
        $data = file_get_contents($path);
        if (false === $data) {
            return false;
        }
        
        // 获取时间
        $expire = (int) substr($data, 0, 12);
        if (time() > $expire) {
            return false;
        }
        
        // 文件校验
        $check = substr($data, 12, 32);
        
        // 压缩选项
        $gz = substr($data, 44, 1);
        
        // md5校验
        $value = substr($data, 45);
        if ($check != md5($value)) {
            return false;
        }
        
        // 解压，反序列化
        if ($gz) {
            $value = gzuncompress($value);
            $value = unserialize($value);
        }
        
        return $value;
    }
    
    public function delete($fileName) {
        
        $this->checkFile($fileName);
        
        $path = $this->getPath($fileName);
        return unlink($path);
    }
}
