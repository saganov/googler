<?php

require_once dirname(dirname(__FILE__))."/src/SearchController.php";
//require_once dirname(dirname(__FILE__))."/src/DatabaseModel.php";
require_once dirname(dirname(__FILE__))."/src/dbengines/PdoEngine.php";
require_once dirname(dirname(__FILE__))."/src/GooglerModel.php";
require_once dirname(dirname(__FILE__))."/src/SearchModel.php";

class App
{
    protected $controller;
    protected $method;
    
    public function __construct($method)
    {

        $config = parse_ini_file(dirname(dirname(__FILE__))."/config/config.ini", true);

        $db = new PdoEngine($config['DB']['name'], $config['DB']['user'], $config['DB']['password']);
        $this->controller = new SearchController(
            new SearchModel($db), //new DatabaseModel(dirname(dirname(__FILE__)).'/data/DB.csv'),
                      // number of the google search result to parse
            new GooglerModel($db->select('source_domain'), 15),
            // result items per page to display
            10);

        $this->method = $method .'Action';
    }
    
    public function run()
    {
        if(is_callable(array($this->controller, $this->method)))
        {
            $arguments = array();
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
