<?php

require_once __DIR__ . '/../../abs/bootstrap.php';

if (PasswordConfig::PASSWORD != '') {
    echo 'You had already installed!';
    exit;
}

if (U('Http')->isAjax()) {
    
    // check extension
    if (!function_exists('mb_strlen')) {
        U('Http')->output(array('valid' => false, 'message' => '请开启mb_string扩展'), 'json');
        exit;
    }
    if (!class_exists('mysqli')) {
        U('Http')->output(array('valid' => false, 'message' => '请开启mysqli扩展'), 'json');
        exit;
    }
    
    // get http params
    $host               = U('Http')->getPOST('mysql-host');
    $username           = U('Http')->getPOST('mysql-username');
    $password           = U('Http')->getPOST('mysql-password');
    $port               = U('Http')->getPOST('mysql-port');
    $managerPassword    = U('Http')->getPOST('password');
    if (empty($host) || empty($username) || empty($password) || empty($port) || empty($managerPassword)) {
        U('Http')->output(array('valid' => false, 'message' => '请您填写完整！'), 'json');
        exit;
    }
    
    // check mysqli
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
    $mysqli = new Mysqli($host, $username, $password, '', $port);
    if ($mysqli->connect_error) {
        U('Http')->output(array('valid' => false, 'message' => 'Mysql连接失败，请您确认配置！'), 'json');
        exit;
    }
    
    // avoid covering database
    $ret = $mysqli->select_db('quickback_blog');
    if ($ret) {
        U('Http')->output(array('valid' => false, 'message' => '数据库quickback_blog已经存在！'), 'json');
        exit;
    }
    
    $sql = "CREATE DATABASE `quickback_blog`;
    USE `quickback_blog`;
    CREATE TABLE `article_list` (
      `article_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
      `category` tinyint(4) NOT NULL DEFAULT '0' COMMENT '文章分类',
      `title` varchar(100) NOT NULL DEFAULT '' COMMENT '标题',
      `tags` varchar(100) NOT NULL DEFAULT '' COMMENT '标签',
      `content` text COMMENT '文章内容，html格式',
      `pv` int(11) NOT NULL DEFAULT '0' COMMENT '浏览量',
      `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
      `hidden` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否隐藏，0显示，1隐藏',
      PRIMARY KEY (`article_id`),
      KEY `category` (`category`) USING BTREE,
      KEY `title` (`title`) USING BTREE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    CREATE TABLE `log_list` (
      `log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'logId，自增',
      `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'log分类',
      `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '记录日志的时间',
      `level` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'log的级别',
      `pid` int(11) NOT NULL DEFAULT '0' COMMENT '服务端php端口',
      `remote_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '客户端ip',
      `remote_port` int(11) NOT NULL DEFAULT '0' COMMENT '客户端端口',
      `loc` varchar(300) NOT NULL DEFAULT '' COMMENT '发生错误的文件位置',
      `url` varchar(300) NOT NULL DEFAULT '' COMMENT '客户端请求的url',
      `request` text COMMENT '客户端请求的request参数',
      `message` text COMMENT '记录的错误描述',
      PRIMARY KEY (`log_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
    $sql = str_replace(array("\n", "\r", "\r\n"), '', $sql);
    $sqlList = explode(';', $sql);
    foreach ($sqlList as $str) {
        if (empty($str)) {
            continue;
        }
        $ret = $mysqli->query($str);
        if (false === $ret) {
            U('Http')->output(array('valid' => false, 'message' => $mysqli->error), 'json');
            exit;
        }
    }
    
    $code = <<<dbConfigCode
<?php

namespace ABS;

class DbConfig {
    
    // 开启sql调试的话，生成的SQL语句都会记录到SLOG后台，仅用在开发
    public static \$SAVE_SQL = false;
    
    // 主服务器
    public static \$WEB_MASTER = array(
        'host'     => '{$host}',
        'username' => '{$username}',
        'password' => '{$password}',
        'port'     => {$port},
    );
    
    // 从服务器
    public static \$WEB_SLAVE = array(
        'host'     => '{$host}',
        'username' => '{$username}',
        'password' => '{$password}',
        'port'     => {$port},
    );
}

dbConfigCode;

    $filePath = ABS_PATH . '/base/config/DbConfig.class.php';
    $ret = file_put_contents($filePath, $code);
    if (false == $ret) {
        U('Http')->output(array('valid' => false, 'message' => '创建DbConfig文件失败！'), 'json');
        exit;
    }
    
    $code = <<<passwordConfig
<?php

class PasswordConfig {
    
    const PASSWORD = '{$password}';
}
passwordConfig;
    
    $filePath = ABS_PATH . '/base/config/PasswordConfig.class.php';
    $ret = file_put_contents($filePath, $code);
    if (false == $ret) {
        U('Http')->output(array('valid' => false, 'message' => '创建GlobalConfig失败！'), 'json');
        exit;
    }
    
    U('Http')->output(array('valid' => true, 'message' => 'success'), 'json');
    exit;
}


?>


<!DOCTYPE html>
<html>
<head>
    <title>Install</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link type="text/css" href="/static/css/common/init.css" rel="stylesheet">
    <link type="text/css" href="/static/css/www/framework.page.css" rel="stylesheet">
    <script src="/static/js/cgi/sea.js"></script>
</head>
<body>
    <div class="module-wrap">
        <div class="module-wrap2">
            <form class="widget-form bg-white w600" style="margin: 20px auto;padding: 20px 0 10px 0;">
                <div class="item tc f16" style="padding: 20px;">一键配置 Quick Back</div>
                <div class="item">
                    <label class="label w180">MbString检测：</label>
                    <label class="label"><?php echo function_exists('mb_strlen') ? '<font class="green">支持</font>' : '<font class="red">不支持</font>'; ?></label>
                </div>
                <div class="item">
                    <label class="label w180">Mysqli检测：</label>
                    <label class="label"><?php echo class_exists('mysqli') ? '<font class="green">支持</font>' : '<font class="red">不支持</font>'; ?></label>
                </div>
                <div class="item">
                    <label class="label w180">Mysql 地址：</label>
                    <input name="mysql-host" class="input w300" type="text" value="127.0.0.1" />
                </div>
                <div class="item">
                    <label class="label w180">Mysql 用户：</label>
                    <input name="mysql-username" class="input w300" type="text" value="root" />
                </div>
                <div class="item">
                    <label class="label w180">Mysql 密码：</label>
                    <input name="mysql-password" class="input w300" type="text" value="123" />
                </div>
                <div class="item">
                    <label class="label w180">Mysql 端口：</label>
                    <input name="mysql-port" class="input w300" type="text" value="3306" />
                </div>
                <div class="item">
                    <label class="label w180">博客管理密码：</label>
                    <input name="password" class="input w300" type="password" />
                </div>
                <div class="item mt20">
                    <input name="submit" class="btn btn-blue" style="width: 100%;" type="submit" value="写入配置" />
                </div>
            </form>
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
    seajs.use(['jquery', 'notice'], function($, notice) {
        $('input[name=submit]').click(function(e) {
            e.preventDefault();
            $.ajax({
                'url': '/install.php',
                'type': 'post',
                'dataType': 'json',
                'data': {
                    'mysql-host': $('input[name=mysql-host]').val(),
                    'mysql-username': $('input[name=mysql-username]').val(),
                    'mysql-password': $('input[name=mysql-password]').val(),
                    'mysql-port': $('input[name=mysql-port]').val(),
                    'password': $('input[name=password]').val()
                },
                'success': function(result) {
                    if (result.valid) {
                        notice('success', '配置成功，即将跳转到首页', 1, function() {
                            location.href = '/';
                        });
                    } else {
                        notice('error', result.message);
                    }
                },
                'error': function() {
                    notice('error', '服务器请求失败！');
                }
                
            });
        })
    });
</script>
