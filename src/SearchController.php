<?php

require_once dirname(__FILE__).'/ActionHelper.php';

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

    public function listAction($query = NULL, $source = NULL)
    {
        $helper = new ActionHelper;
        $view = new View('body.html.php');
        try
        {
            if(!$this->cache->isQueryExists($query))
            {
                /** @todo: let the cache insert returned the count of the inserted items
                 *         to avoid redundant database interaction
                 */
                $this->cache->insertList($query, $this->googler->get($query));
            }
            
            $result = $this->cache->getList($query,
                                            $source,
                                            0,
                                            $this->itemsPerPage);

            $url_ids = array();
            $url_ids['search'] = $this->extract($result['search'], 'id');
            $url_ids['news']   = $this->extract($result['news'],   'id');
            $url_ids['youtube']= $this->extract($result['youtube'],'id');
            $unshown = $helper->getUnshown($url_ids);
            if(!empty($unshown['search']) || !empty($unshown['news']) || !empty($unshown['youtube']))
            {
                $helper->addShown($unshown);
                $this->cache->updateList($unshown);
                $this->update($result['search'], 'show', '+1');
                $this->update($result['news'],   'show', '+1');
                $this->update($result['youtube'],'show', '+1');
            }
            
            $content = new View('list.html.php');
            $content->set(array('query'=>$query,
                                'source'=>$source,
                                'items'=>$result));
        }
        catch(Exception $e)
        {
            $content = new View('error.html.php');
            $content->set(array('query'=>$query, 'message'=>'Error occurs: '.$e->getMessage()));
        }

        $view->set(array('content'=>$content->parse()));
        $view->output();
    }

    public function listSearchAction($query = NULL, $source = NULL, $page = 0)
    {
        $helper = new ActionHelper;
        $view = new View('body.html.php');
        try
        {
            $count = $this->cache->countListSearch($query, $source);
            
            $result = $this->cache->getList($query,
                                            $source,
                                            $this->itemsPerPage * $page,
                                            $this->itemsPerPage);

            $url_ids = array();
            $url_ids['search'] = $this->extract($result['search'], 'id');
            $url_ids['news']   = array();
            $url_ids['youtube']   = array();
            $unshown = $helper->getUnshown($url_ids);
            if(!empty($unshown['search']))
            {
                $helper->addShown($unshown);
                $this->cache->updateList($unshown);
                $this->update($result['search'], 'show', '+1');
            }
            
            $content = new View('list_search.html.php');
            $content->set(array('query'=>$query,
                                'source'=>$source,
                                'page'=>$page+1,
                                'total'=>ceil($count/$this->itemsPerPage),
                                'items'=>$result['search']));
        }
        catch(Exception $e)
        {
            $content = new View('error.html.php');
            $content->set(array('query'=>$query, 'page'=>$page+1, 'message'=>'Error occurs: '.$e->getMessage()));
        }

        $view->set(array('content'=>$content->parse()));
        $view->output();
    }

    public function listNewsAction($query = NULL, $source = NULL, $page = 0)
    {
        $helper = new ActionHelper;
        $view = new View('body.html.php');
        try
        {
            $count = $this->cache->countListNews($query, $source);
            
            $result = $this->cache->getList($query,
                                            $source,
                                            $this->itemsPerPage * $page,
                                            $this->itemsPerPage);

            $url_ids = array();
            $url_ids['search'] = array();
            $url_ids['news']   = $this->extract($result['news'],   'id');
            $url_ids['youtube'] = array();
            $unshown = $helper->getUnshown($url_ids);
            if(!empty($unshown['news']))
            {
                $helper->addShown($unshown);
                $this->cache->updateList($unshown);
                $this->update($result['news'],   'show', '+1');
            }
            
            $content = new View('list_news.html.php');
            $content->set(array('query'=>$query,
                                'source'=>$source,
                                'page'=>$page+1,
                                'total'=>ceil($count/$this->itemsPerPage),
                                'items'=>$result['news']));
        }
        catch(Exception $e)
        {
            $content = new View('error.html.php');
            $content->set(array('query'=>$query, 'page'=>$page+1, 'message'=>'Error occurs: '.$e->getMessage()));
        }

        $view->set(array('content'=>$content->parse()));
        $view->output();
    }

    public function listYoutubeAction($query = NULL, $source = NULL, $page = 0)
    {
        $helper = new ActionHelper;
        $view = new View('body.html.php');
        try
        {
            $count = $this->cache->countListYoutube($query, $source);
            
            $result = $this->cache->getList($query,
                                            $source,
                                            $this->itemsPerPage * $page,
                                            $this->itemsPerPage);

            $url_ids = array();
            $url_ids['search']  = array();
            $url_ids['news']    = array();
            $url_ids['youtube'] = $this->extract($result['youtube'],   'id');
            $unshown = $helper->getUnshown($url_ids);
            if(!empty($unshown['youtube']))
            {
                $helper->addShown($unshown);
                $this->cache->updateList($unshown);
                $this->update($result['youtube'],   'show', '+1');
            }
            
            $content = new View('list_youtube.html.php');
            $content->set(array('query'=>$query,
                                'source'=>$source,
                                'page'=>$page+1,
                                'total'=>ceil($count/$this->itemsPerPage),
                                'items'=>$result['youtube']));
        }
        catch(Exception $e)
        {
            $content = new View('error.html.php');
            $content->set(array('query'=>$query, 'page'=>$page+1, 'message'=>'Error occurs: '.$e->getMessage()));
        }

        $view->set(array('content'=>$content->parse()));
        $view->output();
    }

    public function ajaxAction($url, $table)
    {
        $helper = new ActionHelper;
        if($helper->isUniqueClick($url, $table))
        {
            $helper->addClicked($url, $table);
            $this->cache->update($table, array('click'=>'`click`+1'), array('url' => $url));
            $rows = $this->cache->select($table, array('url'=>$url), 0, 1);
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