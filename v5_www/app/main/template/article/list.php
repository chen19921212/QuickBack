<form class="widget-form bg-gray mb10">
    <div class="item">
        <div class="fl">
            <label class="label">标题：</label>
            <input name="title" class="input w400" type="text" value="<?php echo U('Http')->getGET('title'); ?>" />
            <input name="category" type="hidden" value="<?php echo U('Http')->getGET('category'); ?>" />
            <input type="submit" class="w100 btn" value="查找" />
        </div>
        <div class="fr">
            
        </div>
        <div style="clear:both;"></div>
    </div>
</form>

<style>
    .widget-table th {
        background-color: #f5f5f5;
    }
</style>

<table class="widget-table mb10">
    <thead>
        <tr>
            <th width="10%" class="tc">文章ID</th>
            <th width="60%">标题</th>
            <th width="10%" class="tc">浏览量</th>
            <th width="20%">创建时间</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($this->articleList as $articleId => $articleInfo) { ?>
            <tr>
                <td class="tc"><?php echo $articleId; ?></td>
                <td>
                    <p class="f14"><a href="/main/article_detail/?article-id=<?php echo $articleId; ?>"><?php echo $articleInfo['title']; ?></a></p>
                    <p>标签：<?php echo $articleInfo['tags']; ?></p>
                </td>
                
                <td class="tc"><?php echo $articleInfo['pv']; ?></td>
                <td>
                    <p><?php echo date('Y-m-d', $articleInfo['create_time']) . ' ' . U('Time')->getWeekCn($articleInfo['create_time']); ?></p>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<?php echo $this->html['pager']; ?>

<script>
    seajs.use(['jquery', 'layer', 'notice'], function($, layer, notice) {
        
        $('select[name=category]').change(function(e) {
            e.preventDefault();
            var url = '/main/manager_article_list/?category=' + $(this).val();
            location.href = url;
        });
        
        $('input[name=add-article]').click(function(e) {
            e.preventDefault();
            var url = '/main/manager_article_add/iframeAdd/';
            $.layer({
                type: 2,
                title: '添加一篇文章',
                border: [1, 1, '#ddd'],
                iframe: {src : url },
                area: ['800px' , '250px']
            });
        });
        
        $('a[name=show]').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: '/main/manager_article_list/ajaxShow/',
                type: 'post',
                dataType: 'json',
                data: {
                    'article-id': $(this).attr('article-id')
                },
                success: function(result) {
                    if (result.valid) {
                        notice('success', '操作成功', 1, function() {
                            location.reload();
                        });
                    } else {
                        notice('error', result.message);
                    }
                },
                error: function() {
                    notice('error', '服务器请求失败！');
                }
            });
        });
        
        $('a[name=hide]').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: '/main/manager_article_list/ajaxHide/',
                type: 'post',
                dataType: 'json',
                data: {
                    'article-id': $(this).attr('article-id')
                },
                success: function(result) {
                    if (result.valid) {
                        notice('success', '操作成功', 1, function() {
                            location.reload();
                        });
                    } else {
                        notice('error', result.message);
                    }
                },
                error: function() {
                    notice('error', '服务器请求失败！');
                }
            });
        });
        
    });
</script>