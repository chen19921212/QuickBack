<!DOCTYPE html>
<html>
<head>
    <title>QuickBack Qback</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link type="text/css" href="/static/css/common/init.css" rel="stylesheet">
    <link type="text/css" href="/static/css/www/framework.page.css" rel="stylesheet">
    <script src="/static/js/cgi/sea.js"></script>
</head>
<body>
    <div class="module-wrap">
        <div class="module-wrap2">
            <div class="sidebar">
                <div class="nav" <?php echo empty($this->mainTags) ? 'id="sidebar-fixed"' : ''; ?> >
                    <div class="logo"><a href="/"><img src="/static/image/www/logo.png"></a></div>
                    <div class="menu">
                        <ul>
                            <li><a href="/" <?php if (U('Url')->getPath() == '/') echo  'class="hover"' ; ?> ><span class="text">首页</span></a></li>
                            <?php foreach (BlogVars::$CATEGORY as $key => $value) { ?>
                                <li><a href="/main/article_list/?category=<?php echo $key; ?>" <?php if (U('Url')->getPath() == '/main/article_list/' && U('Http')->getGET('category') == $key) echo  'class="hover"' ; ?> ><span class="text"><?php echo $value; ?></a></span></li>
                            <?php } ?>
                            <li><a href="/main/about/" <?php if (U('Url')->getPath() == '/main/about/') echo  'class="hover"' ; ?> ><span class="text">关于我</a></span></li>
                        </ul>
                    </div>
                    <?php if ($this->isLogin) { ?>
                    <div class="manager">
                        <div class="title">后台</div>
                        <ul>
                            <li><a href="/main/manager_article_list/" <?php if (U('Url')->getPath() == '/main/manager_article_list/') echo  'class="hover"' ; ?> ><span class="text">管理文章</a></span></li>
                            <li><a href="/main/manager_log_list/" <?php if (U('Url')->getPath() == '/main/manager_log_list/') echo  'class="hover"' ; ?> ><span class="text">网站日志</a></span></li>
                        </ul>
                    </div>
                    <?php } ?>
                    <?php if (!$this->isLogin && U('Url')->getPath() == '/') { ?>
                    <div class="check">
                        <a name="check-in" href="#">Check In</a>
                    </div>
                    <?php } ?>
                </div>
                <?php if (!empty($this->mainTags)) { ?>
                <div id="sidebar-fixed" class="tags">
                    <div class="title">分类标签</div>
                    <div class="tag-list">
                        <?php foreach ($this->mainTags as $key => $value) { ?><a <?php echo $this->currentTag == $key ? 'class="hover"' : ''; ?> href="<?php echo U('Url')->make(U('Url')->getCurrentUrl(), array('tag' => $key, 'page'=> null,)); ?>"><?php echo $key; ?></a><?php } ?>
                        <div style="clear:both;"></div>
                    </div>
                </div>
                <?php } ?>
            </div>
            <div class="main">
                <?php echo $this->mainContent; ?>
                <div style="clear:both;"></div>
            </div>
            <div style="clear:both;"></div>
        </div>
    </div>
    <div class="module-footer f14">
        <div class="module-footer2">
            <!-- QuickBack采用GPL协议 -->
            <!-- 写代码不易，如果采用QuickBack，请注明来源，谢谢了！ -->
            <p>Copyright © 2015-2015 <a target="_blank" href="https://github.com/aozhongxu/QuickBack">QuickBack</a>. All Rights Reserved.</p>
        </div>
    </div>
</body>
</html>

<script>
    seajs.use(['jquery', 'layer', 'notice'], function($, layer, notice) {
        
        $('a[name=check-in]').click(function(e) {
            e.preventDefault();
            var url = '/main/check/iframeCheck/';
            $.layer({
                type: 2,
                title: '输入管理密码',
                border: [1, 1, '#ddd'],
                iframe: {src : url },
                area: ['600px' , '110px'],
                shift: 'top',
                close: function(index){
                    window.location.reload();
                }
            });
        });
        
        var bindHeaderScroll = function() {
            var top = $('#sidebar-fixed').offset().top;
            window.onscroll = function() {
                if ($(window).scrollTop() > top-10) {
                    $('#sidebar-fixed').addClass('fixed');
                } else {
                    $('#sidebar-fixed').removeClass('fixed');
                }
            };
        };
        bindHeaderScroll();
    });
</script>