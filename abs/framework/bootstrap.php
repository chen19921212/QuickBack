<?php

define('ABS_PATH', dirname(__DIR__));
define('STO_PATH', dirname(ABS_PATH) . '/storage');

// 载入ABS
require_once ABS_PATH . '/bootstrap.php';

// 载入framework
require_once ABS_PATH . '/framework/BasePage.class.php';
require_once ABS_PATH . '/framework/Router.class.php';
