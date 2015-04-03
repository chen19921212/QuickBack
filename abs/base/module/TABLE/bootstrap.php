<?php

/**
 * @brief 使用TABLE的时候，载入此文件即可
 */

function TABLE($tableName) {
    
    require_once ABS_PATH . '/base/config/DbConfig.class.php';
    require_once __DIR__ . '/core/BaseModel.class.php';
    require_once __DIR__ . '/core/MysqliExt.class.php';
    require_once __DIR__ . '/core/SqlBuilder.class.php';
    
    $arr = explode('_', $tableName);
    $db = $arr[0];
    $className = '';
    foreach ($arr as $v) {
        $className .= ucfirst($v);
    }
    $className .= 'Model';
    $file = __DIR__ . '/model/' . $db . '/' . $className . '.class.php';
    require_once $file;
    $model = "ABS\\{$className}";
    return $model::obj();
}
