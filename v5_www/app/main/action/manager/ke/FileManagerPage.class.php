<?php

require_once APP_PATH . '/action/manager/include/ManagerBasePage.class.php';

class FileManagerPage extends ManagerBasePage {
    
    public function ajaxAction() {
        
        // 获取参数
        $dirName    = U('Http')->getGET('dir', '');
        $order      = U('Http')->getGET('order', 'name');
        $getPath    = U('Http')->getGET('path');
        
        $rootPath = STA_PATH . '/upload/blog/';
        $rootUrl = '/static/upload/blog/';
        
        $extArr = array('gif', 'jpg', 'jpeg', 'png', 'bmp', 'ico');
        
        if (!in_array($dirName, array('', 'image', 'flash', 'media', 'file'))) {
            echo "Invalid Directory name.";
            exit;
        }
        
        if ($dirName !== '') {
            $rootPath .= $dirName . '/';
            $rootUrl .= $dirName . '/';
            if (!file_exists($rootPath)) {
                mkdir($rootPath, 0777, true);
            }
        }
        
        if (empty($getPath)) {
            $currentPath = $rootPath;
            $currentUrl = $rootUrl;
            $currentDirPath = '';
            $moveupDirPath = '';
        } else {
            $currentPath = $rootPath . $getPath;
            $currentUrl = $rootUrl . $getPath;
            $currentDirPath = $getPath;
            $moveupDirPath = preg_replace('/(.*?)[^\/]+\/$/', '$1', $currentDirPath);
        }
        
        
        
        if (preg_match('/\.\./', $currentPath)) {
            echo 'Access is not allowed.';
            exit;
        }
        
        if (!preg_match('/\/$/', $currentPath)) {
            echo 'Parameter is not valid.';
            exit;
        }
        
        if (!file_exists($currentPath) || !is_dir($currentPath)) {
            echo 'Directory does not exist.';
            exit;
        }
        
        //遍历目录取得文件信息
        $fileList = array();
        if ($handle = opendir($currentPath)) {
            $i = 0;
            while (false !== ($filename = readdir($handle))) {
                if ($filename{0} == '.') {
                    continue;
                }
                $file = $currentPath . $filename;
                if (is_dir($file)) {
                    $fileList[$i]['is_dir'] = true; //是否文件夹
                    $fileList[$i]['has_file'] = (count(scandir($file)) > 2); //文件夹是否包含文件
                    $fileList[$i]['filesize'] = 0; //文件大小
                    $fileList[$i]['is_photo'] = false; //是否图片
                    $fileList[$i]['filetype'] = ''; //文件类别，用扩展名判断
                } else {
                    $fileList[$i]['is_dir'] = false;
                    $fileList[$i]['has_file'] = false;
                    $fileList[$i]['filesize'] = filesize($file);
                    $fileList[$i]['dir_path'] = '';
                    $fileExt = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    $fileList[$i]['is_photo'] = in_array($fileExt, $extArr);
                    $fileList[$i]['filetype'] = $fileExt;
                }
                $fileList[$i]['filename'] = $filename; //文件名，包含扩展名
                $fileList[$i]['datetime'] = date('Y-m-d H:i:s', filemtime($file)); //文件最后修改时间
                $i++;
            }
            closedir($handle);
        }
        
        //排序
        function cmpFunc($a, $b) {
            global $order;
            if ($a['is_dir'] && !$b['is_dir']) {
                return -1;
            } else if (!$a['is_dir'] && $b['is_dir']) {
                return 1;
            } else {
                if ($order == 'size') {
                    if ($a['filesize'] > $b['filesize']) {
                        return 1;
                    } else if ($a['filesize'] < $b['filesize']) {
                        return -1;
                    } else {
                        return 0;
                    }
                } else if ($order == 'type') {
                    return strcmp($a['filetype'], $b['filetype']);
                } else {
                    return strcmp($a['filename'], $b['filename']);
                }
            }
        }
        usort($fileList, 'cmpFunc');
        
        $result = array(
            'moveup_dir_path' => $moveupDirPath,
            'current_dir_path' => $currentDirPath,
            'current_url' => $currentUrl,
            'total_count' => count($fileList),
            'file_list' => $fileList,
        );
        echo json_encode($result);
        exit;
    }
}
