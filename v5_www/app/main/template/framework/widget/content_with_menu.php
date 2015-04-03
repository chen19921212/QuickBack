<?php

$currentPath = U('Url')->getPath();

$navLink = '<font>首页</font>';
foreach ($this->ojSecMenu as $item) {
    if (!key_exists($currentPath, $item['menu'])) {
        continue;
    }
    foreach ($item['menu'] as $url => $menuItem) {
        if ($currentPath == $url) {
            if (isset($menuItem['hidden']) && $menuItem['hidden']) {
                $navLink = sprintf('%s &gt; <font>%s</font>', $item['title'], $menuItem['title']);
            } else {
                $navLink = sprintf('%s &gt; <a href="%s">%s</a>', $item['title'], $url, $menuItem['title']);
            }
            break;
        }
    }
    break;
}

?>

<div class="module-breadcrumb">
    <a href="<?php echo $this->currentAppInfo['url']; ?>"><?php echo $this->currentAppInfo['title']; ?></a> &gt;
    <?php echo $navLink; ?>
</div>
<div class="mt10">
    <div class="module-menu">
        <?php   foreach ($this->ojSecMenu as $item) { ?>
            <div class="item">
                <div class="title"><?php echo $item['title']; ?></div>
                <ul>
                    <?php
                            foreach ($item['menu'] as $url => $menuItem) {
                                if (isset($menuItem['hidden']) && $menuItem['hidden']) {
                                    continue;
                                }
                                $class = $currentPath == $url ? $class = 'class="selected"' : '';
                    ?>
                            <li><a <?php echo $class; ?> href="<?php echo $url; ?>"><?php echo $menuItem['title']; ?></a></li>
                    <?php   } ?>
                </ul>
            </div>
        <?php   } ?>
    </div>
    <div class="module-menu-content">
        <?php echo $this->ojSecContent; ?>
    </div>
</div>