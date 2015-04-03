<?php

class ArticleApi {
    
    public static function getArticleInfo($articleId) {
        
        if (empty($articleId) || !U('Number')->isInt($articleId)) {
            return false;
        }
        $where = array(
            array('article_id', '=', $articleId),
        );
        $articleInfo = TABLE('article_list')->getRow('*', $where);
        return $articleInfo;
    }
    
    public static function updateArticle($articleId, $updateData) {
        
        if (empty($articleId) || !U('Number')->isInt($articleId) || empty($updateData)) {
            return false;
        }
        $where = array(
            array('article_id', '=', $articleId),
        );
        $ret = TABLE('article_list')->update($updateData, $where);
        return $ret;
    }
    
    public static function getGlobalInfo() {
        
        $cacheKey = 'blog/global_count_cache/count';
        
        $ret = U('Data')->get($cacheKey);
        if (false !== $ret) {
            return $ret;
        }
        
        // 文章总数
        $articleCount = TABLE('article_list')->getCount();
        
        // 标签总数
        $tagsList = TABLE('article_list')->getList('article_id, tags', $where);
        $tags = array();
        foreach ($tagsList as $tagsInfo) {
            $arr = explode(',', $tagsInfo['tags']);
            foreach ($arr as $item) {
                $key = trim($item);
                $tags[$key]++;
            }
        }
        $tagCount = count($tags);
        
        // 浏览总数
        $articleInfo = TABLE('article_list')->getRow('sum(pv) as sum_pv');
        $pvCount = $articleInfo['sum_pv'];
        
        $ret = array(
            'article_count' => (int) $articleCount,
            'tag_count'     => (int) $tagCount,
            'pv_count'      => (int) $pvCount,
        );
        
        U('Data')->set($cacheKey, $ret, time()+3600);
        return $ret;
    }
}
