<?php

require_once dirname(dirname(__FILE__))."/src/SearchController.php";
require_once dirname(dirname(__FILE__))."/src/DatabaseModel.php";
require_once dirname(dirname(__FILE__))."/src/GooglerModel.php";

class App
{
    protected $controller;
    protected $method;
    
    public function __construct($method)
    {
        $this->controller = new SearchController(
            new DatabaseModel(dirname(dirname(__FILE__)).'/data/DB.csv'),
            new GooglerModel(array('en.wikipedia.org', 'ru.wikipedia.org', 'lurkmore.to')));

        $this->method = $method .'Action';
    }
    
    public function run()
    {
        if(is_callable(array($this->controller, $this->method)))
        {
            $arguments = array()
            foreach(func_get_args() as $arg)
            {
                $arguments[] = $arg;
            }

            return call_user_func_array(
                array($this->controller, $this->method),
                $arguments);
        }
    }

}
