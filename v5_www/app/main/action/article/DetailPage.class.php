<?php

class DetailPage extends MainBasePage {
    
    public function defaultAction() {
        
        $articleId = U('Http')->getGET('article-id');
        
        $articleInfo = ArticleApi::getArticleInfo($articleId);
        if (empty($articleInfo)) {
            $this->renderMain404();
            return false;
        }
        
        // PV+1
        ArticleApi::updateArticle($articleId, 'pv=pv+1');
        
        $this->renderMain(array(
            'articleInfo'   => $articleInfo,
        ), 'article/detail.php');
    }
}
