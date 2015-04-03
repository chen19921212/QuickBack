<?php

namespace ABS;

class Upload {
    
    private static $instance = null;
    
    public static function obj() {
        
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {}
    private function __clone() {}
    
    /**
     * @brief   移动上传的文件
     * @param   field   表单域
     * @param   file    目的路径
     */
    public function move($field, $file) {
        
        $tmpFile = $this->getTmpName($field);
        if (empty($tmpFile)) {
            return false;
        }
        $dir = dirname($file);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return move_uploaded_file($tmpFile, $file);
    }
    
    /**
     * @brief   获取单个上传文件的临时路径
     */
    public function getTmpName($field) {
        
        $tmpName  = $_FILES[$field]['tmp_name'];
        if (empty($tmpName)) {
            return false;
        }
        return $tmpName;
    }
    
    /**
     * @brief   获取单个上传文件的大小
     */
    public function getFilesize($field) {
        
        $filesize = $_FILES[$field]['size'];
        if (empty($filesize)) {
            return false;
        }
        return $filesize;
    }
    
    /**
     * @brief   获取上传文件的文件名
     */
    public function getFilename($field) {
        
        $filename = $_FILES[$field]['name'];
        if (empty($filename)) {
            return false;
        }
        return $filename;
    }
    
    /**
     * @brief   获取单个上传文件的扩展名
     */
    public function getFileExt($field) {
        
        $filename = $_FILES[$field]['name'];
        if (empty($filename)) {
            return false;
        }
        $arr = explode('.', $filename);
        return end($arr);
    }
    
}
