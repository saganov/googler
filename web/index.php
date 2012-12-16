<?php

if (php_sapi_name() == 'cli-server' && preg_match('/\.(?:png|jpg|jpeg|gif|js|css)$/', $_SERVER["REQUEST_URI"]))
{
    return false;    // serve the requested resource as-is.
}
else
{ 
    $query  = isset($_REQUEST['q']) ? $_REQUEST['q'] : NULL;
    $source = isset($_REQUEST['s']) ? $_REQUEST['s'] : NULL;
    $page   = isset($_REQUEST['p']) ? $_REQUEST['p'] : 0;
    $url    = isset($_REQUEST['url']) ? $_REQUEST['url'] : NULL;

    /** @todo: think about determine what should to do
     *         based on REQUEST method: GET, POST, etc.
     */
    $method = isset($_REQUEST['m']) ? $_REQUEST['m'] : 'index';
    
    require_once (dirname(dirname(__FILE__)) ."/app/App.php");
    require_once (dirname(dirname(__FILE__))."/src/View.php");
    
    View::setTemplateDir(dirname(dirname(__FILE__)) ."/view/web");
    
    /** @todo: Replace this code into APP
        @todo: Create the base controller class 
               and store request there 
               and make the method to retrieve any
               request option. This avoid different 
               controller action signatures
     */
    $app = new App($method);
    if($method == 'ajax')
    {
        $app->run($_POST['url'], $_POST['table']);
    }
    elseif($method == 'list')
    {
        $app->run($query, $source);
    }
    elseif(in_array($method, array('listSearch', 'listNews', 'listYoutube')))
    {
        $app->run($query, $source, $page);
    }
    elseif($method == 'embedYoutube')
    {
        $app->run($url);
    }
    else
    {
        $app->run();
    }
}


