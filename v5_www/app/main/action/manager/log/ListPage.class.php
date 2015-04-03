<?php

require_once APP_PATH . '/action/manager/include/ManagerBasePage.class.php';

class ListPage extends ManagerBasePage {
    
    public function defaultAction() {
        
        $pageSize = 20;
        
        $page = max(1, (int)U('Http')->getGET('page', 1));
        $type = (int) U('Http')->getGET('type');
        
        // 构建where
        $where = array();
        if ($type > 0) {
            $where[] = array( 'type', '=', $type );
        }
        
        $offset     = ($page-1)*$pageSize;
        $logList    = TABLE('log_list')->getList('*', $where, 'log_id DESC', $pageSize, $offset);
        $allCount   = TABLE('log_list')->getCount($where);
        
        // 缓存部分的html
        $html = array();
        $html['pager'] = $this->fetch(array(
            'renderAllCount' => $allCount,
            'renderPageSize' => $pageSize,
            'renderRadius'   => 4,
        ), 'widget/pager_long.php');
        
        $this->renderMain(array(
            'html'      => $html,
            'logList'   => $logList,
        ), 'manager/log/list.php');
    }
}
