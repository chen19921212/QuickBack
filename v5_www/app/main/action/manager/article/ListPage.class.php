<?php

require_once APP_PATH . '/action/manager/include/ManagerBasePage.class.php';

class ListPage extends ManagerBasePage {
    
    public function defaultAction() {
        
        $pageSize = 50;
        
        $page       = max(1, (int)U('Http')->getGET('page', 1));
        $category   = (int) U('Http')->getGET('category');
        $title      = U('Http')->getGET('title');
        $status     = U('Http')->getGET('status');
        $tag        = U('Http')->getGET('tag', '');
        
        $where = array();
        if ($category > 0) {
            $where[] = array('category', '=', $category);
        }
        if ($status > 0) {
            $where[] = array('hidden', '=', $status-1);
        }
        if (!empty($tag)) {
            $where[] = array('tags', 'LIKE', "%{$tag}%");
        }
        if (!empty($title)) {
            $where[] = array('title', 'LIKE', "%{$title}%");
        }
        
        $offset = ($page-1)*$pageSize;
        $articleList = TABLE('article_list')->getList('*', $where, 'article_id DESC', $pageSize, $offset);
        $allCount    = TABLE('article_list')->getCount($where);
        
        $this->setMainTags($category, $tag);
        
        // 缓存部分的html
        $html = array();
        $html['pager'] = $this->fetch(array(
            'renderAllCount' => $allCount,
            'renderPageSize' => $pageSize,
            'renderRadius'   => 7,
        ), 'widget/pager.php');
        
        $this->renderMain(array(
            'html'          => $html,
            'articleList'   => $articleList,
        ), 'manager/article/list.php');
    }
    
    public function ajaxShowAction() {
        
        $articleId = U('Http')->getPOST('article-id');
        $articleInfo = ArticleApi::getArticleInfo($articleId);
        if (empty($articleInfo)) {
            $this->renderAjax(false, '文章不存在！');
            return false;
        }
        if (! $articleInfo['hidden']) {
            $this->renderAjax(false, '文章已经显示！');
            return false;
        }
        $data = array(
            'hidden'    => 0,
        );
        $ret = ArticleApi::updateArticle($articleId, $data);
        if (false === $ret) {
            $this->renderAjax(false, '操作失败！');
            return false;
        }
        $this->renderAjax(true, 'Success！');
    }
    
    public function ajaxHideAction() {
        
        $articleId = U('Http')->getPOST('article-id');
        $articleInfo = ArticleApi::getArticleInfo($articleId);
        if (empty($articleInfo)) {
            $this->renderAjax(false, '文章不存在！');
            return false;
        }
        if ($articleInfo['hidden']) {
            $this->renderAjax(false, '文章已经隐藏！');
            return false;
        }
        $data = array(
            'hidden'    => 1,
        );
        $ret = ArticleApi::updateArticle($articleId, $data);
        if (false === $ret) {
            $this->renderAjax(false, '操作失败！');
            return false;
        }
        $this->renderAjax(true, 'Success！');
    }
}
