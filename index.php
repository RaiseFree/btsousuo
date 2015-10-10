<?php
define("APPLICATION_PATH",  dirname(__FILE__));

define("SMARTY_PATH", APPLICATION_PATH."/application/library/Smarty");
define("SMARTY_SYSPLUGINS_DIR", APPLICATION_PATH."/application/library/Smarty/sysplugins/");
//print_r($app->environ());exit;

$app  = new \Yaf\Application(APPLICATION_PATH . "/conf/application.ini");
$app->bootstrap() //call bootstrap methods defined in Bootstrap.php
    ->run();
