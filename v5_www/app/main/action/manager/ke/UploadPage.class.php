<?php

require_once APP_PATH . '/action/manager/include/ManagerBasePage.class.php';

class UploadPage extends ManagerBasePage {
    
    // 允许的文件类型
    private static $ALLOWED = array(
        'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp', 'ico'),
        'flash' => array('swf', 'flv'),
        'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
        'file'  => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'),
    );
    
    public function ajaxAction() {
        
        // 获取dir参数
        $dir = U('Http')->getGET('dir', 'image');
        if (!array_key_exists($dir, self::$ALLOWED)) {
            echo json_encode(array('error' => 1, 'message' => '参数错误！'));
            return false;
        }
        
        // 获取上传的文件
        $field = 'imgFile';
        $filename = U('Upload')->getFilename($field);
        $filesize = U('Upload')->getFilesize($field);
        $tmpName  = U('Upload')->getTmpName($field);
        $fileExt  = U('Upload')->getFileExt($field);
        if (empty($filesize)) {
            echo json_encode(array('error' => 1, 'message' => '请上传文件！'));
            return false;
        }
        
        // 校验格式
        if (!in_array($fileExt, self::$ALLOWED[$dir])) {
            echo json_encode(array('error' => 1, 'message' => '文件格式不支持，无法上传！'));
            return false;
        }
        
        // 保存
        $savaDir = STA_PATH . '/upload/blog/';
        if (!is_dir($savaDir)) {
            @mkdir($savaDir, 0777, true);
        }
        $newFilename = $dir . '/' . date('YmdHis') . '_' . rand(100000, 999999) . '.' . $fileExt;
        $dest = $savaDir . $newFilename;
        $ret = U('Upload')->move($field, $dest);
        if (false === $ret) {
            echo json_encode(array('error' => 1, 'message' => "上传文件失败。"));
            return false;
        }
        $url = '/static/upload/blog/' . $newFilename;
        echo json_encode(array('error' => 0, 'url' => $url));
    }
}
