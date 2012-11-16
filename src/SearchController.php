<?php

class SearchController
{
    protected $db;
    protected $googler;
    
    protected $query;
    protected $source;
    protected $view;

    protected $itemsPerPage = 10;

    public function __construct($db, $googler, $view)
    {
        $this->db = $db;
        $this->googler = $googler;
        $this->view = $view;
    }

    public function indexAction($query = NULL, $source = NULL, $itemsPerPage = 10)
    {
        $this->itemsPerPage = $itemsPerPage;
        $this->query = $query;
        $this->source = $source;
    }
    
    public function listAction($page = 0)
    {
        $clause = array('query_phrase'=>$this->query);
        if(!is_null($this->source))
        {
            $clause['source_domain'] = $source;
        }
        
        $clause = $this->db->makeClause($clause);
        
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
        
            $this->view->header("Search by phrase: '{$this->query}' :: page #{$page}");
            $this->view->output($res);
        }
        catch(Exception $e)
        {
            $this->view->error("Error occurs: ". $e->getMessage());
        }
    }

    protected function search($clause, $from, $limit)
    {
        return $this->db->select($clause, $from, $limit);
    }
    
    
}