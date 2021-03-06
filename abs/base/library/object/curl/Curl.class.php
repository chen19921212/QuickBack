<?php

namespace ABS;

class Curl {
    
    // 当前连接句柄
    private $handle = null;
    
    // 常用的配置，默认值
    private $defaultOption = array(
        CURLOPT_CONNECTTIMEOUT => 10,    // 连接超时10秒
        CURLOPT_TIMEOUT        => 10,    // 执行超时10秒
        CURLOPT_RETURNTRANSFER => true,  // 以字符串形式返回
        CURLOPT_HEADER         => false, // 默认不输出头文件
        CURLOPT_AUTOREFERER    => true,  // 自动设置header中的Referer:信息
        CURLOPT_FOLLOWLOCATION => true,  // 返回跳转的信息
    );
    
    public function __construct() {
        
        $this->handle = curl_init();
        curl_setopt_array($this->handle, $this->defaultOption);
    }
    
    public function setOption($option) {
        
        curl_setopt_array($this->handle, $option);
    }
    
    public function setTimeout($second) {
        
        $option = array(
            CURLOPT_CONNECTTIMEOUT  => $second,
            CURLOPT_TIMEOUT         => $second,
        );
        $this->setOption($option);
    }
    
    public function setCookieFile($file) {
        
        // 创建文件
        if (!is_file($file)) {
            $dir = dirname($file);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            touch($file);
        }
        $option = array(
            CURLOPT_COOKIEFILE => $file,
            CURLOPT_COOKIEJAR  => $file,
        );
        curl_setopt_array($this->handle, $option);
    }
    
    public function login($url, $data) {
        
        return $this->post($url, $data);
    }
    
    public function post($url, $data) {
        
        $data = http_build_query($data);
        $option = array(
            CURLOPT_URL  => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
        );
        curl_setopt_array($this->handle, $option);
        $content = curl_exec($this->handle);
        if (curl_errno($this->handle)) {
            return false;
        }
        return $content;
    }
    
    public function get($url) {
        
        $option = array(
            CURLOPT_URL  => $url,
            CURLOPT_POST => false,
        );
        curl_setopt_array($this->handle, $option);
        $content = curl_exec($this->handle);
        if (curl_errno($this->handle)) {
            return false;
        }
        return $content;
    }
    
    public function close() {
        
        curl_close($this->handle);
    }
    
    public function errno() {
        
        return curl_errno($this->handle);
    }
    
    public function error() {
        
        return curl_error($this->handle);
    }
}
