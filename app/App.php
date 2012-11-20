<?php

require_once dirname(__DIR__)."/src/SearchController.php";
require_once dirname(__DIR__)."/src/DatabaseModel.php";
require_once dirname(__DIR__)."/src/GooglerModel.php";

class App
{
    protected $controller;
    protected $method;
    
    public function __construct($method)
    {
        $this->controller = new SearchController(
            new DatabaseModel(dirname(__DIR__).'/data/DB.csv'),
            new GooglerModel(array('en.wikipedia.org', 'ru.wikipedia.org', 'lurkmore.to')));

        $this->method = $method .'Action';
    }
    
    public function run()
    {
        if(is_callable(array($this->controller, $this->method)))
        {
            return call_user_func_array(
                array($this->controller, $this->method),
                func_get_args());
        }
    }

}
