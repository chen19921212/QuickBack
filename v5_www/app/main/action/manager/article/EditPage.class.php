<?php

require_once APP_PATH . '/action/manager/include/ManagerBasePage.class.php';

class EditPage extends ManagerBasePage {
    
    public function defaultAction() {
        
        $articleId = U('Http')->getGET('article-id');
        
        $articleInfo = ArticleApi::getArticleInfo($articleId);
        if (empty($articleInfo)) {
            $this->renderMain404();
            return false;
        }
        
        $this->renderMain(array(
            'articleInfo'   => $articleInfo,
        ), 'manager/article/edit.php');
    }
    
    public function ajaxSubmitAction() {
        
        // 获取参数
        $articleId  = U('Http')->getPOST('article-id');
        $category   = U('Http')->getPOST('category');
        $title      = trim(U('Http')->getPOST('title'));
        $tags       = trim(U('Http')->getPOST('tags'));
        $content    = U('Http')->getPOST('content', '', true);
        
        if (mb_strlen($content, 'utf8') > 65535) {
            $this->renderAjax(false, '文章大小超出范围！');
            return false;
        }
        
        // 校验
        if (!array_key_exists($category, BlogVars::$CATEGORY)) {
            $this->renderAjax(false, '类别不存在！');
            return false;
        }
        if (empty($title) || mb_strlen($title) > 100) {
            $this->renderAjax(false, 'Title限制1-100个字！');
            return false;
        }
        if (empty($tags) || mb_strlen($tags) > 100) {
            $this->renderAjax(false, 'Tags限制1-100个字！');
            return false;
        }
        
        // 校验article
        $articleInfo = ArticleApi::getArticleInfo($articleId);
        if (empty($articleInfo)) {
            $this->renderAjax(false, '文章不存在！');
            return false;
        }
        
        // 更新
        $data = array(
            'category'  => $category,
            'title'     => $title,
            'tags'      => $tags,
            'content'   => $content,
        );
        $ret = ArticleApi::updateArticle($articleId, $data);
        if (false === $ret) {
            $this->renderAjax(false, '操作失败！');
            return false;
        }
        $this->renderAjax(true, 'Success！');
    }
}
