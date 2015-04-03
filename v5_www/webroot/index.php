<?php

require_once __DIR__ . '/../../abs/framework/bootstrap.php';

// -- install begin --
if ('' == PasswordConfig::PASSWORD) {
    U('Url')->redirect('/install.php');
}
// -- install end --

define('V_PATH', dirname(__DIR__));
define('STA_PATH', V_PATH . '/webroot/static');

$router = new Router();
$router->run();