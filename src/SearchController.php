<?php

require_once "SearchModel.php";
require_once "SearchView.php";

class SearchController
{
    protected $searcher;
    
    protected $query;
    protected $source;

    public function __construct()
    {
        $this->searcher = new SearchModel();
    }

    public function indexAction($query = NULL, $source = NULL)
    {
        $this->query = $query;
        $this->source = $source;
    }
    
    public function listAction($page = 0)
    {
        SearchView::header("Search by phrase: '{$this->query}' :: page #{$page}");
        SearchView::output($this->searcher->get($this->query, $this->source, $page));
    }
    
    
}