<link rel="stylesheet" href="/static/js/plugin/kindeditor/themes/default/default.css" />
<link rel="stylesheet" href="/static/js/plugin/kindeditor/plugins/code/prettify.css" />

<div class="p10 bg-white">
    <div class="tc p10" style="height: 85px; border-bottom: 1px dashed #eee;">
        <h3 class="f20 bg-gray mb10"><?php echo $this->articleInfo['title']; ?></h3>
        <p class="mt10 fl">标签：<?php echo $this->articleInfo['tags']; ?></p>
        <p class="mt10 fr"><?php if ($this->isLogin) { ?><a href="/main/manager_article_edit/?article-id=<?php echo $this->articleInfo['article_id']; ?>">【编辑】</a><?php } ?>浏览次数：<?php echo $this->articleInfo['pv']; ?>&nbsp;&nbsp;|&nbsp;&nbsp;创建时间：<?php echo date('Y-m-d', $this->articleInfo['create_time']) . ' ' . U('Time')->getWeekCn($this->articleInfo['create_time']); ?></p>
    </div>
    <div class="p10"><?php echo $this->articleInfo['content']; ?></div>
</div>

<script charset="utf-8" src="/static/js/plugin/kindeditor/plugins/code/prettify.js"></script>
<script>
    prettyPrint();
</script>