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

    /** @todo: think about determine what should to do
     *         based on REQUEST method: GET, POST, etc.
     */
    $method = isset($_REQUEST['m']) ? $_REQUEST['m'] : 'list'; /** @todo: should be replaced by index */
    
    require_once (dirname(dirname(__FILE__)) ."/app/App.php");
    require_once (dirname(dirname(__FILE__))."/src/View.php");
    
    View::setTemplateDir(dirname(dirname(__FILE__)) ."/view/web");
    
    /** @todo: Replace this code into APP */
    $app = new App($method);
    if($method == 'ajax')
    {
        $app->run($_POST['url']);
    }
    else
    {
        $app->run($query, $source, $page);
    }
}


