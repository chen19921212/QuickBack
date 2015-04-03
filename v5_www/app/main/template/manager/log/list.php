<form class="widget-form bg-gray mb10">
    <label class="label">类型：</label>
    <select name="type" class="select">
        <option value="-1">全部</option>
        <?php   foreach (ABS\SlogConfig::$typeText as $type => $text) {
            $selected = U('Http')->getGET('type') == $type ? 'selected' : '';
        ?>
            <option <?php echo $selected; ?> value="<?php echo $type; ?>"><?php echo $text; ?></option>
        <?php   } ?>
    </select>
</form>

<?php echo $this->html['pager']; ?>

<style>
    .log-table td {
        word-break: break-word;
    }
</style>

<table class="widget-table log-table mt10">
    <thead>
        <tr>
            <th width="8%">ID</th>
            <th width="10%">时间</th>
            <th width="6%">类型</th>
            <th width="6%">级别</th>
            <th width="70%">日志</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($this->logList as $logId => $logInfo) { ?>
        <tr>
            <td><?php echo $logId; ?></td>
            <td><?php echo date('Y-m-d H:i:s', $logInfo['create_time']); ?></td>
            <td>
                <?php echo ABS\SlogConfig::$typeText[$logInfo['type']]; ?>
            </td>
            <td>
                <font class="fw <?php echo ABS\SlogConfig::$levelColor[$logInfo['level']]; ?>">
                    <?php echo ABS\SlogConfig::$levelText[$logInfo['level']]; ?>
                </font>
            </td>
            <td>
                <p><font class="red">Url：</font><?php echo htmlspecialchars($logInfo['url']); ?></p>
                <p><font class="red">Req：</font><?php echo htmlspecialchars($logInfo['request']); ?></p>
                <p><font class="red">IP：</font><?php echo U('Http')->long2ip($logInfo['remote_ip']).':'.$logInfo['remote_port']; ?></p>
                <p><font class="red">Log：</font><?php echo $logInfo['message']; ?></p>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>


<script>
    seajs.use(['jquery'], function($) {
        
        $('select[name=type]').change(function() {
            var url = '/main/manager_log_list/?type=' + $(this).val();
            location.href = url;
        });
    })
</script>
