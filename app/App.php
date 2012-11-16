<?php

require_once dirname(__DIR__)."/src/SearchController.php";
require_once dirname(__DIR__)."/src/DatabaseModel.php";
require_once dirname(__DIR__)."/src/GooglerModel.php";

class App
{
    protected $controller;
    
    public function __construct(/*View Interface*/$view)
    {
        $this->controller = new SearchController(
            new DatabaseModel(dirname(__DIR__).'/data/DB.csv'),
            new GooglerModel(array('en.wikipedia.org', 'ru.wikipedia.org', 'lurkmore.to')),
            $view);
    }
    

    public function run($query, $source, $page)
    {
        $this->controller->indexAction($query, $source);
        $this->controller->listAction($page);
    }

}
