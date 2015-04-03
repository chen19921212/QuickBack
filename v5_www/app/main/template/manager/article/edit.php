<link rel="stylesheet" href="/static/js/plugin/kindeditor/themes/default/default.css" />
<link rel="stylesheet" href="/static/js/plugin/kindeditor/plugins/code/prettify.css" />

<form class="widget-form bg-white">
    <div class="item">
        <label class="label">分类：</label>
        <select name="category" class="select">
            <?php
                foreach (BlogVars::$CATEGORY as $key => $value) {
                    $selected = $key == $this->articleInfo['category'] ? 'selected' : '';
            ?>
                <option <?php echo $selected; ?> value="<?php echo $key; ?>"><?php echo $value; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="item">
        <label class="label">标题：</label>
        <input name="title" class="w400 input" type="text" value="<?php echo $this->articleInfo['title']; ?>" />
    </div>
    <div class="item">
        <label class="label">标签：</label>
        <input name="tags" class="w400 input" type="text" value="<?php echo $this->articleInfo['tags']; ?>" />
    </div>
    <div>
        <label class="label">内容：</label>
    </div>
    <div>
        <textarea name="content" rows="30" style="width: 900px;"><?php echo htmlspecialchars($this->articleInfo['content']); ?></textarea>
    </div>
    <div class="mt10 mb10">
        <input name="edit" type="button" class="btn btn-blue w120" value="提交" />
    </div>
    <input name="article-id" type="hidden" value="<?php echo $this->articleInfo['article_id']; ?>" />
</form>

<script src="/static/js/plugin/kindeditor/kindeditor.js"></script>
<script src="/static/js/plugin/kindeditor/lang/zh_CN.js"></script>

<script>
    seajs.use(['jquery', 'notice'], function($, notice) {
        
        KindEditor.ready(function(K) {
            var editor = K.create('textarea[name=content]', {
                cssPath : '/static/js/plugin/kindeditor/plugins/code/prettify.css',
                uploadJson : '/main/manager_ke_upload/ajax/',
                fileManagerJson : '/main/manager_ke_fileManager/ajax/',
                allowFileManager : true,
                urlType : 'domain',
                afterBlur: function () { this.sync(); }
            });
        });
        
        $('input[name=edit]').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: '/main/manager_article_edit/ajaxSubmit/',
                type: 'post',
                dataType: 'json',
                data: {
                    'article-id': $('input[name=article-id]').val(),
                    'category'  : $('select[name=category]').val(),
                    'title'     : $('input[name=title]').val(),
                    'tags'      : $('input[name=tags]').val(),
                    'content'   : $('textarea[name=content]').val()
                },
                success: function(result) {
                    if (result.valid) {
                        location.href = '/main/article_detail/?article-id=' + $('input[name=article-id]').val();
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