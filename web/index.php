<?php

$query  = isset($_REQUEST['q']) ? $_REQUEST['q'] : NULL;
$source = isset($_REQUEST['s']) ? $_REQUEST['s'] : NULL;
$page   = isset($_REQUEST['p']) ? $_REQUEST['p'] : 0;

require_once dirname(__DIR__) ."/app/App.php";
require_once dirname(__DIR__)."/src/WebView.php";

$app = new App(new WebView);
$app->run($query, $source, $page);
