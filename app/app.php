<?php

require_once "QueryService.php";

$query_service = new QueryService;

if(!empty($_REQUEST['q']) && empty($_REQUEST['s']))
{
        $queries = $query_service->getAll($query);
}