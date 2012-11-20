<?php

class SearchController
{
    protected $db;
    protected $googler;
    
    protected $itemsPerPage = 10;

    public function __construct($db, $googler, $itemsPerPage = 10)
    {
        $this->db = $db;
        $this->googler = $googler;
        $this->itemsPerPage = $itemsPerPage;
    }

    public function indexAction()
    {
        $view = new View('body.html.php');
        $content = new View('index.html.php');
        $content->set(array('sources'=>array('en.wikipedia.org', 'ru.wikipedia.org', 'lurkmore.to')));
        $view->set(array('content'=>$content->parse()));
        $view->output();
    }
    
    public function listAction($query = NULL, $source = NULL, $page = 0)
    {
        $clause = array('query_phrase'=>$query);
        if(!empty($source))
        {
            $clause['source_domain'] = $source;
        }

        $clause = $this->db->makeClause($clause);

        $view = new View('body.html.php');
        try
        {
            $count = $this->db->count($clause);
            if($count < 1)
            {
                $this->db->insert($this->googler->get($query));
                $count = $this->db->count($clause);
            }

            $this->db->update(array('show'=>'+1'),
                              $clause,
                              $this->itemsPerPage * $page, // from line
                              $this->itemsPerPage);        // limit
            
            $res = $this->db->select(
                $clause,                     // where clause
                $this->itemsPerPage * $page, // from line
                $this->itemsPerPage);        // limit
            
            $content = new View('content.html.php');
            $content->set(array('query'=>$query,
                                'source'=>$source,
                                'page'=>$page+1,
                                'total'=>ceil($count/$this->itemsPerPage),
                                'items'=>$res));
        }
        catch(Exception $e)
        {
            $content = new View('error.html.php');
            $content->set(array('query'=>$query, 'page'=>$page+1, 'message'=>'Error occurs: '.$e->getMessage()));
        }

        $view->set(array('content'=>$content->parse()));
        $view->output();
    }

    public function ajaxAction($url)
    {
        $this->db->update(array('click'=>'+1'), array('url' => $url), 0, 1);
        $rows = $this->db->select(array('url'=>$url), 0, 1);
        $ajax = new View('ajax.html.php');
        $ajax->set(array('data' => array('click' => (int)$rows[0]['click'])));
        $ajax->output();
    }
}