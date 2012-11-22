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
        //$content->set(array('sources'=>array('en.wikipedia.org', 'ru.wikipedia.org', 'lurkmore.to')));
        $content->set(array('sources'=>$this->db->select('source_domain')));
        $view->set(array('content'=>$content->parse()));
        $view->output();
    }
    
    public function listAction($query = NULL, $source = NULL, $page = 0)
    {
        $view = new View('body.html.php');
        try
        {
            $query_id = $this->db->select('query_phrase', array('text'=>$query));
            $query_id = isset($query_id[0]) ? $query_id[0]['id'] : FALSE;
            if(!$query_id)
            {
                $new = $this->googler->get($query);
                $this->db->insert('query_phrase', array(array('text'=>$query)));
                $query_id = $this->db->select('query_phrase', array('text'=>$query));
                $query_id = isset($query_id[0]) ? $query_id[0]['id'] : FALSE;
                
                foreach($new as &$n)
                {
                    $n['query_phrase'] = $query_id;
                    $source_id = $this->db->select('source_domain', array('domain'=>$n['source_domain']));
                    $source_id = isset($source_id[0]) ? $source_id[0]['id'] : FALSE;
                    $n['source_domain'] = $source_id;
                }

                $this->db->insert('search_item', $new);
            }

            $clause = array('query_phrase'=>$query_id);
            if(!empty($source))
            {
                $clause['source_domain'] = $source;
            }
            $count = $this->db->count('search_item', $clause);
            $this->db->update('search_item', 
                              array('show'=>'`show`+1'),
                              $clause,
                              $this->itemsPerPage * $page, // from line
                              $this->itemsPerPage);        // limit
            
            $res = $this->db->select('search_item', 
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
        $this->db->update('search_item', array('click'=>'`click`+1'), array('url' => $url));
        $rows = $this->db->select('search_item', array('url'=>$url), 0, 1);
        $ajax = new View('ajax.html.php');
        $ajax->set(array('data' => array('click' => (int)$rows[0]['click'])));
        $ajax->output();
    }
}