<?php

class ListPage extends MainBasePage {
    
    public function defaultAction() {
        
        $pageSize = 50;
        
        $page       = max(1, (int)U('Http')->getGET('page', 1));
        $category   = (int) U('Http')->getGET('category');
        $title      = U('Http')->getGET('title');
        $tag        = U('Http')->getGET('tag', '');
        
        if (!array_key_exists($category, BlogVars::$CATEGORY)) {
            $this->renderMain404();
            return false;
        }
        
        $where = array();
        if ($category > 0) {
            $where[] = array('category', '=', $category);
        }
        $where[] = array('hidden', '=', 0);
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
        ), 'article/list.php');
    }
}
