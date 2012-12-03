<?php

require_once __DIR__.'/ActionHelper.php';

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

        ini_set('magic_quotes_gpc', '0');
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
    
    protected function extract(array $data, $key)
    {
        $res = array();
        foreach($data as $item)
        {
            if(isset($item[$key]))
            {
                $res[] = $item[$key];
            }
        }
        return $res;
    }

    protected function update(array &$data, $key, $operation)
    {
        $res = array();
        foreach($data as &$item)
        {
            if(isset($item[$key]))
            {
                switch($operation)
                {
                    case '+1':
                        ++$item[$key];
                        break;
                }
            }
        }
        unset($item);
    }

    public function listAction($query = NULL, $source = NULL, $page = 0)
    {
        $view = new View('body.html.php');
        try
        {
            $count = $this->cache->countList($query, $source);
            if($count < 1)
            {
                /** @todo: let the cache insert returned the count of the inserted items
                 *         to avoid redundant database interaction
                 */
                $this->cache->insertList($query, $this->googler->get($query));
            }
            $count = $this->cache->countList($query, $source);
            
            $result = $this->cache->getList($query,
                                            $source,
                                            $this->itemsPerPage * $page,
                                            $this->itemsPerPage);

            $urls = $this->extract($result, 'url');
            $unshown = ActionHelper::getUnshown($urls);
            if(!empty($unshown))
            {
                ActionHelper::addShown($unshown);
                $this->cache->updateList($unshown);
                $this->update($result, 'show', '+1');
            }
            
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
        if(ActionHelper::isUniqueClick($url))
        {
            ActionHelper::addClicked($url);
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