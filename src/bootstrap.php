<?php

require_once "SearchController.php";

$controller = new SearchController();

$controller->indexAction('test');

$controller->listAction();

$controller->indexAction('another');

$controller->listAction();