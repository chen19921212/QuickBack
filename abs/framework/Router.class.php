<?php

/**
 * @brief 路由器，考虑到子类拓展和安全性，这里使用单例
 */

class Router {
    
    // 默认的app目录
    const DEFAULT_APP = 'main';
    
    public static $APP          = ''; // 应用名：back
    public static $PAGE         = ''; // 页面名：shop/list, shop/add
    public static $ACTION       = ''; // 动作名：show, ajaxSubmit
    public static $IS_AJAX      = false;
    public static $IS_IFRAME    = false;
    public static $CONTENT_TYPE = 'html';
    
    public static $CLASS_NAME   = ''; // 执行的页面类，如：IndexPage
    
    public function __construct() {
        
        // 开启定时器
        U('Time')->p('PHP Begin');
        
        // 解析URL
        $this->parseUrl();
        
        // 配置路径
        $this->init();
    }
    
    /**
     * @brief   提供V_PATH，APP_PATH的计算方式
     */
    protected function init() {
        
        // 定义全局变量
        define('APP_PATH', V_PATH . '/app/' . self::$APP);
    }
    
    protected function parseUrl() {
        
        // _sys_url_path = /back/shop_list/show/
        // 通过Rewrite得到，实现index.php单一入口
        $url = U('Http')->getGET('_sys_url_path', '');
        $paths = explode('/', $url);
        
        // 过滤，url只能由字母数字下划线组成，不能为空
        $params = array();
        foreach ($paths as $path) {
            $regex = '/^[a-zA-z0-9\_]+$/';
            if (!preg_match($regex, $path)) {
                continue;
            }
            $params[] = $path;
        }
        
        // 只分离出前3个
        if (count($params) == 0){
            $params[] = self::DEFAULT_APP; // 默认app，通常不使用
        }
        if (count($params) == 1){
            $params[] = 'index';
        }
        if (count($params) == 2){
            $params[] = 'default';
        }
        
        // 获取$PARAMS
        self::$APP       = $params[0];
        self::$PAGE      = str_replace('_', '/', $params[1]);  // URL中可以使用下划线来实现实现多级目录，这里需要转换下划线为斜杠
        self::$ACTION    = $params[2];
        self::$IS_AJAX   = strpos(self::$ACTION, 'ajax') === 0 ? true : false;
        self::$IS_IFRAME = strpos(self::$ACTION, 'iframe') === 0 ? true : false;
        self::$CONTENT_TYPE = self::$IS_AJAX ? 'json' : 'html';
    }
    
    public function run() {
        
        header('Content-type: text/html; charset=utf-8');
        
        // run
        $className = basename(self::$PAGE);
        $dir = ($className == self::$PAGE) ? '' : dirname(self::$PAGE) . '/';
        $className = ucfirst($className) . 'Page';
        self::$CLASS_NAME = $className;
        
        $bootFile  = APP_PATH . '/common/bootstrap.php';
        $classFile = APP_PATH . '/action/' . $dir . $className . '.class.php';
        if (!is_file($bootFile) || !is_file($classFile)) {
            if (self::$IS_AJAX) {
                U('Http')->output(array( 'valid'   => false, 'message' => '页面不存在！', ), 'json');
            } else {
                U('Url')->redirect(ABS\GlobalConfig::PAGE404);
            }
        }
        require_once $bootFile;
        require_once $classFile;
        $obj = new $className;
        $actionName = self::$ACTION . 'Action';
        if (false == method_exists($obj, $actionName)) {
            if (self::$IS_AJAX) {
                U('Http')->output(array( 'valid'   => false, 'message' => '页面不存在！', ), 'json');
            } else {
                U('Url')->redirect(ABS\GlobalConfig::PAGE404);
            }
        }
        $obj->$actionName();
        
        // 定时器结束
        U('Time')->p('PHP End');
        
        if (U('Http')->getGET('_debugtime')) {
            U('Time')->show();
        }
    }
}