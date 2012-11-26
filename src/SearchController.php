<?php

class SearchController
{
    protected $cache;
    protected $googler;
    
    protected $itemsPerPage = 10;
    
    public function __construct($cache, $googler, $itemsPerPage = 10)
    {
        $this->cache = $cache;
        $this->googler = $googler;
        $this->itemsPerPage = $itemsPerPage;
    }

    public function indexAction()
    {
        $view = new View('body.html.php');
        $content = new View('index.html.php');
        //$content->set(array('sources'=>array('en.wikipedia.org', 'ru.wikipedia.org', 'lurkmore.to')));
        $content->set(array('sources'=>$this->cache->select('source_domain')));
        $view->set(array('content'=>$content->parse()));
        $view->output();
    }
    
    protected function key($query = NULL, $source = NULL, $page = 0)
    {
        return $query.'_'.$source.'_'.$page;
    }

    protected function isUniqueVisit($query = NULL, $source = NULL, $page = 0)
    {
        $key = $this->key($query, $source, $page);
        $search = (isset($_COOKIE['search']) ? json_decode($_COOKIE['search'], TRUE) : array());
        return (!isset($search[$key]));
    }

    protected function isUniqueClick($url)
    {
        $click = (isset($_COOKIE['click']) ? json_decode($_COOKIE['click'], TRUE) : array());
        return (!isset($click[$url]));
    }

    protected function addVisit($query = NULL, $source = NULL, $page = 0)
    {
        $key = $this->key($query, $source, $page);
        $search = (isset($_COOKIE['search']) ? json_decode($_COOKIE['search'], TRUE) : array());
        $search[$key] = time();
        View::debug('cookie', $search);
        return json_encode($search);
    }

    protected function addClick($url)
    {
        $click = (isset($_COOKIE['click']) ? json_decode($_COOKIE['click'], TRUE) : array());
        $click[$url] = time();
        return json_encode($click);
    }

    public function listAction($query = NULL, $source = NULL, $page = 0)
    {
        $view = new View('body.html.php');
        try
        {
            $count = $this->cache->countList($query);
            if($count < 1)
            {
                $this->cache->insertList($query, $this->googler->get($query));
            }
            $count = $this->cache->countList($query);
            
            if($this->isUniqueVisit($query, $source, $page))
            {
                View::setcookie('search', $this->addVisit($query, $source, $page));
                $this->cache->updateList($query,
                                         $source,
                                         $this->itemsPerPage * $page, // from line
                                         $this->itemsPerPage);        // limit
            }
            $result = $this->cache->getList($query,
                                            $source,
                                            $this->itemsPerPage * $page,
                                            $this->itemsPerPage);
            
            $content = new View('content.html.php');
            $content->set(array('query'=>$query,
                                'source'=>$source,
                                'page'=>$page+1,
                                'total'=>ceil($count/$this->itemsPerPage),
                                'items'=>$result));
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
        if($this->isUniqueClick($url))
        {
            View::setcookie('click', $this->addClick($url));
            $this->cache->update('search_item', array('click'=>'`click`+1'), array('url' => $url));
            $rows = $this->cache->select('search_item', array('url'=>$url), 0, 1);
            $ajax = new View('ajax.html.php');
            $ajax->set(array('data' => array('click' => (int)$rows[0]['click'])));
            $ajax->output();
        }
        else
        {
            $ajax = new View('ajax.html.php');
            $ajax->set(array('data' => array('click'=>FALSE)));
            $ajax->output();
        }
    }
}