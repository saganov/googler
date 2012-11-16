<?php

require_once "SearchView.php";

class SearchController
{
    protected $db;
    protected $googler;
    
    protected $query;
    protected $source;

    protected $itemsPerPage = 10;

    public function __construct($db, $googler)
    {
        $this->db = $db;
        $this->googler = $googler;
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
        
            SearchView::header("Search by phrase: '{$this->query}' :: page #{$page}");
            SearchView::output($res);
        }
        catch(Exception $e)
        {
            SearchView::error("Error occurs: ". $e->getMessage());
        }
    }

    protected function search($clause, $from, $limit)
    {
        return $this->db->select($clause, $from, $limit);
    }
    
    
}