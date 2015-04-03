<link type="text/css" href="/static/css/www/index.page.css" rel="stylesheet">

<div class="index-col1">
    <?php foreach ($this->articleList as $articleId => $articleInfo) { ?>
        <div class="list-item">
            <div class="title"><a href="/main/article_detail/?article-id=<?php echo $articleId; ?>"><?php echo $articleId . '. ' . $articleInfo['title']; ?></a></div>
            <div class="list-item-row">
                <div class="fl">类别：<?php echo BlogVars::$CATEGORY[$articleInfo['category']]; ?></div>
                <div class="fr f14">发表于：<?php echo date('Y-m-d', $articleInfo['create_time']) . ' ' . U('Time')->getWeekCn($articleInfo['create_time']); ?></div>
                <div style="clear: both;"></div>
            </div>
            <div class="list-item-row">标签：<?php echo $articleInfo['tags']; ?></div>
            <div class="list-item-row"><a href="/main/article_detail/?article-id=<?php echo $articleId; ?>">查看更多>></a></div>
        </div>
    <?php } ?>
    <?php echo $this->html['pager']; ?>
</div>
<div class="index-col2">
    <div class="row-1">
        <a href="https://github.com/aozhongxu/QuickBack" target="_blank" title="Github" ><img src="/static/image/www/show.png" width="246px" /></a>
    </div>
    <div class="row-2">
        <div class="title">站点信息</div>
        <div class="info">
            <p>文章总数：<?php echo $this->globalInfo['article_count']; ?>篇</p>
            <p>标签总数：<?php echo $this->globalInfo['tag_count']; ?>个</p>
            <p>浏览总数：<?php echo $this->globalInfo['pv_count']; ?>次</p>
        </div>
    </div>
    <div class="row-3">
        <div class="title">友情链接</div>
        <div class="info">
            <div class="info-row">
                <a href="http://www.qback.cn/" target="_blank">QBack官方博客</a>
                <a href="https://github.com/aozhongxu" target="_blank">GitHub</a>
            </div>
        </div>
    </div>
</div>
