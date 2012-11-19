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


require_once (dirname(__DIR__)."/app/App.php");
require_once (dirname(__DIR__)."/src/View.php");

View::setTemplateDir(dirname(__DIR__) ."/view/cli");

$app = new App();
$app->run($query, $source, $page);
