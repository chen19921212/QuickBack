<?php

abstract class MainBasePage extends AppPage {
    
    protected $isLogin  = array();
    protected $mainTags = array();
    protected $currentCategory  = 0;
    protected $currentTag       = '';
    
    public function __construct() {
        
        parent::__construct();
        
        $this->isLogin = UcApi::checkLogin();
        
        // assign
        $this->assign(array(
            'isLogin'   => $this->isLogin,
            'mainTags'  => $this->mainTags,
        ));
    }
    
    protected function checkLogin() {
        
        if (! $this->isLogin) {
            U('Url')->redirect('/404.html');
        }
    }
    
    /**
     * @brief   解析tags
     * @param   $category   int     当前的category
     * @param   $selected   string  当前选中的tags
     */
    protected function setMainTags($currentCategory = 0, $currentTag = '') {
        
        $this->currentCategory  = $currentCategory;
        $this->currentTag       = $currentTag;
        $this->assign(array(
            'currentCategory'   => $this->currentCategory,
            'currentTag'        => $this->currentTag,
        ));
        
        // 获取类目下的tag
        $where = array();
        if ($currentCategory > 0) {
            $where[] = array('category', '=', $currentCategory);
        }
        if (!$this->isLogin) {
            $where[] = array('hidden', '=', 0);
        }
        
        $tagsList = TABLE('article_list')->getList('article_id, tags', $where);
        
        // 计算tags
        $tags = array();
        foreach ($tagsList as $tagsInfo) {
            $arr = explode(',', $tagsInfo['tags']);
            foreach ($arr as $item) {
                $key = trim($item);
                $tags[$key]++;
            }
        }
        asort($tags);
        $tags = array_reverse($tags, true);
        $this->mainTags = $tags;
        $this->assign(array(
            'mainTags'  => $this->mainTags,
        ));
    }
    
    protected function renderMain($params, $tpl) {
        
        $content = $this->fetch($params, $tpl);
        
        $this->render(array(
            'mainContent' => $content,
        ), 'framework/framework.php');
    }
    
    protected function renderMain404($message = '你要访问的页面不存在！') {
        
        $this->renderMain(array(
            'message' => $message,
        ), 'framework/widget/404.php');
    }
    
    public function renderIframe($params, $tpl = '') {
        
        // 获取内容
        $content = $this->fetch($params, $tpl);
        $this->render(array(
            'iframeContent' => $content,
        ), 'framework/framework_iframe.php');
    }

    public function renderIframe404($message = '你要访问的页面不存在！') {
        
        $this->renderIframe(array(
            'iframeContent' => $message,
        ), 'framework/widget/iframe_404.php');
    }
}
