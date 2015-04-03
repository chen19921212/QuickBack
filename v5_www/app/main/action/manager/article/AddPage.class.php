<?php

require_once APP_PATH . '/action/manager/include/ManagerBasePage.class.php';

class AddPage extends ManagerBasePage {
    
    public function iframeAddAction() {
        
        $this->renderIframe(array(), 'manager/article/iframe/add.php');
    }
    
    public function ajaxSubmitAction() {
        
        $category   = U('Http')->getPOST('category');
        $title      = U('Http')->getPOST('title');
        $tags       = U('Http')->getPOST('tags');
        
        $data = array(
            'category'      => $category,
            'title'         => $title,
            'tags'          => $tags,
            'create_time'   => time(),
            'hidden'        => 1,
        );
        $ret = TABLE('article_list')->insert($data);
        if (false === $ret) {
            $this->renderAjax(false, '插入数据库失败！');
            return false;
        }
        $this->renderAjax(true, 'Success');
    }
}
