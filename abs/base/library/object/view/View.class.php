<?php

namespace ABS;

/**
 * @brief 视图类
 */

class View {
    
    private $commonDir = '';
    private $dir       = '';
    
    /**
     * @brief 构造函数
     */
    public function __construct() {
        
    }
    
    /**
     * @brief 设置共有模板和私有模板，也可以只使用私有模板
     */
    public function init($dir, $commonDir = '') {
        
        if (!is_dir($dir)) {
            trigger_error("私有模板目录{$dir}不存在！", E_USER_ERROR);
        }
        if (!empty($commonDir) && !is_dir($commonDir)) {
            trigger_error("公共模板目录{$commonDir}不存在！", E_USER_ERROR);
        }
        $this->commonDir = $commonDir;
        $this->dir = $dir;
        return $this;
    }
    
    private function getFilePath($tpl) {
        
        $path = $this->dir . '/' . $tpl;
        if (!is_file($path)) {
            $path = $this->commonDir . '/' . $tpl;
            if (!is_file($path)) {
                trigger_error("模板文件{$tpl}不存在！", E_USER_ERROR);
            }
        }
        return $path;
    }
    
    /**
     * @brief 将参数解析到对象的成员变量
     */
    public function assign($params) {
        
        if (!is_array($params)) {
            trigger_error('解析参数必须为关联数组', E_USER_ERROR);
        }
        foreach ($params as $key => $value) {
            $this->$key = $value;
        }
    }
    
    /**
     * @brief 获取并解析模板，View的核心方法
     */
    public function fetch($params, $tpl) {
        
        $path = $this->getFilePath($tpl);
        $this->assign($params);
        
        ob_start();
        include $path;
        $ret = ob_get_contents();
        ob_end_clean();
        return $ret;
    }
}
