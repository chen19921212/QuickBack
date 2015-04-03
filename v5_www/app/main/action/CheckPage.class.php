<?php

class CheckPage extends MainBasePage {
    
    public function iframeCheckAction() {
        
        $this->renderIframe(array(), 'iframe/check.php');
    }
    
    public function ajaxCheckAction() {
        
        // 获取密码
        $password = U('Http')->getPOST('password');
        
        $ret = UcApi::login($password);
        if (false === $ret) {
            $this->renderAjax(false, '密码错误！');
            return false;
        }
        $this->renderAjax(true, 'Success');
    }
}
