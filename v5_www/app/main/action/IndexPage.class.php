<?php

class IndexPage extends MainBasePage {
    
    public function defaultAction() {
        
        $pageSize = 20;
        
        $page       = max(1, (int)U('Http')->getGET('page', 1));
        
        $where = array();
        $where[] = array('hidden', '=', 0);
        
        $offset = ($page-1)*$pageSize;
        $articleList = TABLE('article_list')->getList('*', $where, 'article_id DESC', $pageSize, $offset);
        $allCount    = TABLE('article_list')->getCount($where);
        
        // 缓存部分的html
        $html = array();
        $html['pager'] = $this->fetch(array(
            'renderAllCount' => $allCount,
            'renderPageSize' => $pageSize,
            'renderRadius'   => 3,
        ), 'widget/pager_white.php');
        
        $this->renderMain(array(
            'html'          => $html,
            'articleList'   => $articleList,
            'globalInfo'    => ArticleApi::getGlobalInfo(),
        ), 'index.php');
    }
}
