<?php

namespace ABS;

class Url {
    
    private static $instance = null;
    
    public static function obj() {
        
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {}
    private function __clone() {}
    
    // 域名类型，长的放前面
    private $topLevelDomain = array(
        'com.cn',
        'com',
        'net',
        'cn',
    );
    
    /**
     * @brief   代码中的重定向都使用302
     */
    public function redirect($url) {
        
        header('HTTP/1.1 302 Moved Temporarily');
        header('Location: ' . $url);
        exit;
    }
    
    public function refresh() {
        
        $url = $_SERVER['REQUEST_URI'];
        header('HTTP/1.1 302 Moved Temporarily');
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * @brief   创建一个URL
     * @param   url     base_url
     * @param   otherParams  支持重写
     */
    public function make($url = '', $otherParams) {
        
        $queryStr = $this->getQueryString($url, $otherParams);
        $domain   = $this->getDomain($url, true, true);
        $path     = $this->getPath($url);
        $wenhao   = empty($queryStr) ? '' : '?';
        return $domain . $path . $wenhao . $queryStr;
    }
    
    /**
     * @brief   获取当前完整url
     * @param   $otherParams 支持对参数进行重新赋值
     * @return  string
     */
    public function getCurrentUrl($otherParams = array()) {
        
        $domain = $this->getDomain('', true, true);
        if (empty($otherParams)) {
            return $domain . $_SERVER['REQUEST_URI'];
        }
        $path     = $this->getPath();
        $queryStr = $this->getQueryString('', $otherParams);
        $wenhao   = empty($queryStr) ? '' : '?';
        return $domain . $path . $wenhao . $queryStr;
    }
    
    /**
     * @brief   获取主机名
     *          比如当前url为http://xx.domain.com/list/?page=2&user=1，那么返回http://xx.domain.com
     * @param   $url            地址
     * @param   $withScheme     返回值是否带协议
     * @param   $port           是否带端口号，如果是80端口，就不会返回
     * @return  string
     */
    public function getDomain($url = '', $withScheme = false, $withPort = false) {
        
        // 如果给定url
        if (!empty($url)) {
            $urlArr = parse_url($url);
            $ret = $urlArr['host'];
            if (empty($ret)) {
                return false;
            }
            if ($withScheme && !isset($urlArr['scheme'])) {
                $ret = $urlArr['scheme'] . '://' . $urlArr['host'];
            }
            if ($withPort) {
                if (!isset($urlArr['port'])) {
                    $ret = $ret . ':80';
                } else {
                    $ret = $ret . ':' . $urlArr['port'];
                }
            }
            return strtolower($ret);
        }
        
        return $this->getCurrentDomain($withScheme, $withPort);
    }
    
    public function getCurrentDomain($withScheme = false, $withPort = false) {
        
        // HTTP_HOST是带端口的
        $hostArr = explode(':', $_SERVER['HTTP_HOST']);
        $domain = $hostArr[0];
        if ($withScheme) {
            if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                $domain = 'https://' . $domain;
            } else {
                $domain = 'http://' . $domain;
            }
        }
        if ($withPort) {
            $domain = $domain . ':' . $_SERVER['SERVER_PORT'];
        }
        return strtolower($domain);
    }
    
    /**
     * @brief  比如当前url为http://xx.domain.com/list/?page=2&user=1，那么返回/list/
     * @param  $url 如果为空，那么取当前url
     * @return string
     */
    public function getPath($url = '') {
        
        $url = empty($url) ? $_SERVER['REQUEST_URI'] : $url;
        $urlArr = parse_url($url);
        $path = isset($urlArr['path']) ? $urlArr['path'] : '';
        return $path;
    }
    
    /**
     * @brief  获取给定url的参数列表
     *         比如给定url为http://xx.domain.com/list/?page=2&user=1，那么返回page=2&user=1
     * @param  $url 如果为空，那么取当前url
     * @param  $otherParams 支持对参数进行重新赋值
     * @return string
     */
    public function getQueryString($url = '', $otherParams = array()) {
        
        $url = empty($url) ? $_SERVER['REQUEST_URI'] : $url;
        $urlArr = parse_url($url);
        $queryString = isset($urlArr['query']) ? $urlArr['query'] : '';
        $params = array();
        parse_str($queryString, $params);
        if (!empty($otherParams) && is_array($otherParams)) {
            foreach ($otherParams as $key => $value) {
                $params[$key] = $value;
            }
        }
        return http_build_query($params);
    }
    
    /**
     * @brief   获取给定url的参数列表
     * @param   $url    string  需要获取的url，如果url为当前url，那么这个函数相当于$_GET
     * @param   $key    string  需要获取的参数
     * @return  string
     */
    public function getQueryParam($url, $key) {
        
        if (empty($url) || empty($key)) {
            return false;
        }
        $urlArr = parse_url($url);
        $queryString = isset($urlArr['query']) ? $urlArr['query'] : '';
        $params = array();
        parse_str($queryString, $params);
        return $params[$key];
    }
    
    /**
     * @brief   获取域名
     * @param   $level int 0顶级，1当前域名，2二级域名，以此类推
     */
    public function getLevelDomain($url = '', $level = 0) {
        
        $domain = $this->getDomain($url);
        if (empty($domain)) {
            return false;
        }
        if (U('Regex')->checkIp($domain)) {
            return $level > 1 ? false : $domain;
        }
        $suffix = '';
        $rest   = '';
        foreach ($this->topLevelDomain as $value) {
            if ($value == substr($domain, -strlen($value))) {
                $suffix = $value;
                break;
            }
        }
        if (empty($suffix)) {
            $arr = explode('.', $domain);
            $suffix = array_pop($arr);
        }
        $rest = substr($domain, 0, strlen($domain)-strlen($suffix)-1);
        if ($level == 0) {
            return $suffix;
        }
        $restArr = explode('.', $rest);
        if (count($restArr) <= $level) {
            return $rest . '.' . $suffix;
        }
        $ret = $suffix;
        for ($i = 1; $i <= $level; $i++) {
            $ret = $restArr[count($restArr)-$i] . '.' . $ret;
        }
        return $ret;
    }
    
    /**
     * @brief   获取给定url的一级域名以上的前缀（second level domain)
     * @param   $url        地址，如果为空，那么取当前地址
     * @return  string
     */
    public function getSLD($url = '') {
        
        $domain = $this->getDomain($url);
        if (empty($domain)) {
            return false;
        }
        if (U('Regex')->checkIp($domain)) {
            return $level > 1 ? false : $domain;
        }
        $suffix = '';
        $rest = '';
        foreach ($this->topLevelDomain as $value) {
            if ($value == substr($domain, -strlen($value))) {
                $suffix = $value;
                break;
            }
        }
        if (empty($suffix)) {
            $arr = explode('.', $domain);
            $suffix = array_pop($arr);
        }
        $rest = substr($domain, 0, strlen($domain)-strlen($suffix)-1);
        $domainArr = explode('.', $rest);
        array_pop($domainArr);
        return implode('.', $domainArr);
    }
    
    /**
     * @brief   根据url获取到app，这里定义uri的第一个子目录为app
     * @param   $url        url
     * @param   $default    如果uri为根路径，那么返回的默认一级子目录
     * @return  string
     */
    public function getApp($url, $default = 'main') {
        
        $path = $this->getPath($url);
        $pathArr = explode('/', $url);
        
        // 校验，url只能由字母数字下划线组成，不能为空
        foreach ($pathArr as $app) {
            $regex = '/^[a-zA-z0-9\_]+$/';
            if (preg_match($regex, $app)) {
                return $app;
            }
        }
        return $default;
    }
}