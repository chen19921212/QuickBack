<?php

/**
 * @brief 页面基类，约定了基本方法，已经模板的目录设置
 */

abstract class BasePage {
    
    // 视图对象，私有
    private $view = null;
    
    protected function __construct() {
        
        // 继承BasePage的页面都使用这2个模板目录
        $dir       = APP_PATH . '/template';
        $commonDir = V_PATH . '/template';
        $this->view = O('View')->init($dir, $commonDir);
    }
    
    protected function assign($params) {
        
        $this->view->assign($params);
    }
    
    protected function fetch($params, $tpl) {
        
        return $this->view->fetch($params, $tpl);
    }
    
    protected function render($params, $tpl) {
        
        echo $this->view->fetch($params, $tpl);
        
        // 考虑到输出模版后，可能还会进行操作一些，这里不再exit
        // exit;
    }
    
    protected function renderAjax($valid, $message, $params = array()) {
        
        $ret = array(
            'valid'   => $valid,
            'message' => $message,
            'params'  => $params,
        );
        U('Http')->output($ret, 'json');
    }
}