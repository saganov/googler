<?php

class SearchController
{
    protected $db;
    protected $googler;
    
    protected $itemsPerPage = 10;

    public function __construct($db, $googler)
    {
        $this->db = $db;
        $this->googler = $googler;
    }

    public function indexAction($itemsPerPage = 10)
    {
        $this->itemsPerPage = $itemsPerPage;
    }
    
    public function listAction($query = NULL, $source = NULL, $page = 0)
    {
        $clause = array('query_phrase'=>$query);
        if(!is_null($source))
        {
            $clause['source_domain'] = $source;
        }
        
        $clause = $this->db->makeClause($clause);
        
        $view = new View('index.html.php');
        try
        {
            $res = $this->search(
                $clause,                     // where clause
                $this->itemsPerPage * $page, // from line
                $this->itemsPerPage);        // limit

            if(empty($res))
            {
                $this->db->insert($this->googler->get($this->query));
                /** @todo: think - how to improve here */
                $res = $this->search(
                    $clause,                     // where clause
                    $this->itemsPerPage * $page, // from line
                    $this->itemsPerPage);        // limit
            }
        
            $content = new View('content.html.php');
            $content->set(array('query'=>$query, 'page'=>$page+1, 'items'=>$res));
        }
        catch(Exception $e)
        {
            $content = new View('error.html.php');
            $content->set(array('query'=>$query, 'page'=>$page+1, 'message'=>'Error occurs: '.$e->getMessage()));
        }

        $view->set(array('content'=>$content->parse()));
        $view->output();
    }

    protected function search($clause, $from, $limit)
    {
        return $this->db->select($clause, $from, $limit);
    }
    
    
}