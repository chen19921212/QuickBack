<form id="form1" class="widget-form mt10" style="border: 0;">
    <div class="item">
        <label class="label w120">类别：</label>
        <select name="category" class="select" data-validation="number" data-validation-allowing="range[1;100]" data-validation-error-msg="请选择">
            <option value="-1">-</option>
            <?php foreach (BlogVars::$CATEGORY as $key => $value) { ?>
                <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="item">
        <label class="label w120">标题：</label>
        <input name="title" class="input w400" type="text" data-validation="length"  data-validation-length="1-100" />
    </div>
    <div class="item">
        <label class="label w120">标签：</label>
        <input name="tags" class="input w400" type="text" data-validation="length"  data-validation-length="1-100" />
    </div>
    <div class="item">
        <input name="submit" class="btn btn-blue w110 ml130" type="submit" value="添加" />
        <input name="cancel" class="btn w110" type="button" value="取消" />
    </div>
    
</form>

<script>
    seajs.use(['jquery', 'jquery.form-validator', 'notice'], function($, fn1, notice) {
        
        fn1($);
        
        var index = parent.layer.getFrameIndex(window.name);
        
        $('input[name=cancel]').click(function() {
            parent.layer.close(index);
        });
        
        $.validate({
            'form' : '#form1',
            'onSuccess' : function() {
                $.ajax({
                    url: '/main/manager_article_add/ajaxSubmit/',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'category': $('select[name=category]').val(),
                        'title': $('input[name=title]').val(),
                        'tags': $('input[name=tags]').val()
                    },
                    success: function(result) {
                        if (result.valid) {
                            notice('success', '添加成功', 1, function() {
                                parent.location.reload();
                                parent.layer.close(index);
                            });
                        } else {
                            notice('error', result.message);
                        }
                    },
                    error: function() {
                        notice('error', '服务器请求失败！');
                    }
                });
                return false;
            }
        });
    })
</script>