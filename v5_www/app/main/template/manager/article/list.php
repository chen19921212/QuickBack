<form class="widget-form bg-gray mb10">
    <div class="item">
        <div class="fl">
            <label class="label w60">类别：</label>
            <select name="category" class="select">
                <option value="-1">-</option>
                <?php   foreach (BlogVars::$CATEGORY as $key => $value) {
                             $selected = U('Http')->getGET('category') == $key ? 'selected' : '';
                ?>
                    <option <?php echo $selected; ?> value="<?php echo $key; ?>"><?php echo $value; ?></option>
                <?php   } ?>
            </select>
            <label class="label">标题：</label>
            <input name="title" class="input w220" type="text" value="<?php echo U('Http')->getGET('title'); ?>" />
            <label class="label">状态：</label>
            <select name="status" class="select">
                <option value="-1">-</option>
                <option <?php echo U('Http')->getGET('status') == 1 ? 'selected' : ''; ?> value="1">显示</option>
                <option <?php echo U('Http')->getGET('status') == 2 ? 'selected' : ''; ?> value="2">隐藏</option>
            </select>
            <input type="submit" class="w100 btn" value="查找" />
        </div>
        <div class="fr">
            <input name="add-article" type="button" class="w120 btn btn-blue" value="写文章" />
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
            <th width="10%">创建时间</th>
            <th width="6%">状态</th>
            <th width="14%">OP</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($this->articleList as $articleId => $articleInfo) { ?>
            <tr>
                <td class="tc"><?php echo $articleId; ?></td>
                <td>
                    <p class="f14"><a href="/main/article_detail/?article-id=<?php echo $articleId; ?>"><?php echo $articleInfo['title']; ?></a></p>
                    <p>类别：<?php echo BlogVars::$CATEGORY[$articleInfo['category']]; ?>&nbsp;标签：<?php echo $articleInfo['tags']; ?></p>
                </td>
                <td>
                    <p><?php echo date('Y-m-d', $articleInfo['create_time']); ?></p>
                    <p><?php echo U('Time')->getWeekCn($articleInfo['create_time']); ?></p>
                </td>
                <td>
                    <?php if ($articleInfo['hidden']) { ?>
                        <font class="red">隐藏</font>
                    <?php } else { ?>
                        <font class="green">显示</font>
                    <?php } ?>
                </td>
                <td>
                    <?php if ($articleInfo['hidden']) { ?>
                        <a name="show" article-id="<?php echo $articleId; ?>" href="#">显示</a>
                    <?php } else { ?>
                        <a name="hide" article-id="<?php echo $articleId; ?>" href="#">隐藏</a>
                    <?php } ?>
                    <a href="/main/manager_article_edit/?article-id=<?php echo $articleId; ?>">编辑</a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<?php echo $this->html['pager']; ?>

<script>
    seajs.use(['jquery', 'layer', 'notice'], function($, layer, notice) {
        
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
                        location.reload();
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
                        location.reload();
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