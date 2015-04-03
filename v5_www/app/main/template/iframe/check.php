<form class="widget-form" style="text-align: center; margin-top: 10px; border: 0;">
    <div class="item">
        <input name="password" type="password" class="input w220" />
        <input name="submit" class="btn btn-blue w80" type="submit" value="确定" />
    </div>
</form>

<script>
    seajs.use(['jquery', 'notice', 'js/util/crypt/md5.js', 'js/util/crypt/sha1.js'], function($, notice, md5, sha1) {
        
        var index = parent.layer.getFrameIndex(window.name);
        
        $('input[name=submit]').click(function(e) {
            e.preventDefault();
            var password = $('input[name=password]').val();
            password = md5(sha1(password));
            $.ajax({
                url: '/main/check/ajaxCheck/',
                type: 'post',
                dataType: 'json',
                data: {
                    'password': password
                },
                success: function(result) {
                    if (result.valid) {
                        notice('success', '验证成功', 1, function() {
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
        });
        
    });
</script>