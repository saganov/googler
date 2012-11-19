<?php

$query  = isset($_REQUEST['q']) ? $_REQUEST['q'] : NULL;
$source = isset($_REQUEST['s']) ? $_REQUEST['s'] : NULL;
$page   = isset($_REQUEST['p']) ? $_REQUEST['p'] : 0;

require_once (dirname(__DIR__) ."/app/App.php");
require_once (dirname(__DIR__)."/src/View.php");
                                                           
View::setTemplateDir(dirname(__DIR__) ."/view/web");

$app = new App();
$app->run($query, $source, $page);
