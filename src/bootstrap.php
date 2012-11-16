#!/usr/bin/env php
<?php


$usage = "
Syntax
     bootstrap [OPTION]...

Options

  -q QUERY      query phrase
  -s SOURCE     surce domain name
  -p PAGE       page number
  -h            this help
";

$options = getopt("q:s:p:h");

$query  = isset($options['q']) ? $options['q'] : NULL;
$source = isset($options['s']) ? $options['s'] : NULL;
$page   = isset($options['p']) ? $options['p'] : 0;


require_once "SearchController.php";
require_once "DatabaseModel.php";
require_once "GooglerModel.php";

$controller = new SearchController(
    new DatabaseModel('DB.csv'),
    new GooglerModel(array('en.wikipedia.org', 'ru.wikipedia.org', 'lurkmore.to')));

$controller->indexAction($query, $source);

$controller->listAction($page);
