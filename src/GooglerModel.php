<?php

require_once dirname(dirname(__FILE__))."/vendor/phpQuery/phpQuery.php";

class GooglerModel
{
    protected $sources;
    protected $number;

    public function __construct(array $sources, $number = 10)
    {
        $this->sources = $sources;
        $this->number  = $number;
    }

    protected function getPage($url)
    {
        $options = array(
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
//            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "gzip,deflate",       // handle all encodings
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
            CURLOPT_COOKIEFILE     => "cookie.txt",
            CURLOPT_COOKIEJAR      => "cookie.txt",
            //CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3",
            CURLOPT_USERAGENT      => "Mozilla/5.0 (X11; Linux i686; rv:16.0) Gecko/20100101 Firefox/16.0",
            
            //CURLOPT_REFERER        => "http://www.google.com/",
            CURLOPT_REFERER        => $url,
                         );

        $resource = curl_init($url);
        curl_setopt_array($resource, $options);
        $html = curl_exec($resource);
        if (200 != $code = curl_getinfo($resource, CURLINFO_HTTP_CODE))
        {
            //file_put_contents('google.responce', var_export(curl_getinfo($resource), true));
            throw new Exception('Google server returned an unsupported HTTP header: '. $code);
        }
        curl_close($resource); // close the connection 

        return $html;
    }

    protected function search($query, $source)
    {
        $res = array();
        $google_search_url = "https://www.google.com/search?q=site:". urlencode($source .' '.$query);

        $number = 0;
        $page = 0;
        while($page < 4)
        {
            $html = $this->getPage($google_search_url .'&start='. $page);
            $page += 10;
            phpQuery::newDocument($html);
            // all LIs from last selected DOM
            foreach(pq('div#ires')->find('li.g') as $item)
            {
                $number++;
                if($number > $this->number)
                {
                    break 2;
                }
                
                $pq = pq($item);
                
                $title = $pq->find('h3.r > a')->html();
                $url   = $pq->find('h3.r > a')->attr('href');
                if (preg_match('/^.*(http:\/\/.*)$/', $url, $matches))
                {
                    $url = $matches[1];
                }
                $desc  = $pq->find('div.s > span.st')->html();

                $res[] = array(
                    'query_phrase'  => $query,
                    'source_domain' => $source,
                    'url'           => $url,
                    'title'         => $title,
                    'description'   => $desc,
                    'date'          => gmdate('Y-m-d'));
                
            }
        }
        return $res;
    }

    protected function youtube($query)
    {
        $res = array();
        $url = "https://www.youtube.com/results?search_query=". urlencode($query);

        $number = 0;
        $page = 0;
        while($page < 4)
        {
            $html = $this->getPage($url .'&page='. $page);
            $page += 10;
            phpQuery::newDocument($html);
            // all LIs from last selected DOM
            foreach(pq('#search-results')->find('li.context-data-item') as $item)
            {
                $number++;
                if($number > $this->number)
                {
                    break 2;
                }
                
                $pq = pq($item);
                
                $title = $pq->find('h3.yt-lockup2-title > a')->html();
                $url   = $pq->find('h3.yt-lockup2-title > a')->attr('href');
                if (preg_match('/^.*(http:\/\/.*)$/', $url, $matches))
                {
                    $url = $matches[1];
                }
                
                // 
                if (preg_match('/^\/watch\?v=(.*)$/', $url, $matches))
                {
                    $url = $matches[1];
                }

                $source = $pq->find('p.yt-lockup2-meta > a')->html();
                //$date   = $pq->find('div.slp >span.nsa')->html();
                $desc   = $pq->find('p.yt-lockup2-description')->html();
                $thumb  = $pq->find('.video-thumb img')->attr('data-thumb');
                if(empty($thumb))
                {
                    $thumb  = $pq->find('.video-thumb img')->attr('src');
                }       

                $res[] = array(
                    'query_phrase'  => $query,
                    'source_domain' => $source,
                    'url'           => $url,
                    'title'         => $title,
                    'description'   => $desc,
                    'thumb'         => $thumb,
                    'date'          => gmdate('Y-m-d'));
                
            }
        }
        return $res;
    }

    protected function news($query)
    {
        $res = array();
        $google_news_url = "https://www.google.com/search?tbm=nws&as_q=". urlencode($query); //Chuck%20Norris&start=";

        $number = 0;
        $page = 0;
        while($page < 4)
        {
            $html = $this->getPage($google_news_url .'&start='. $page);
            $page += 10;
            phpQuery::newDocument($html);
            // all LIs from last selected DOM
            foreach(pq('div#ires')->find('li.g') as $item)
            {
                $number++;
                if($number > $this->number)
                {
                    break 2;
                }
                
                $pq = pq($item);
                
                $title = $pq->find('h3.r > a')->html();
                $url   = $pq->find('h3.r > a')->attr('href');
                if (preg_match('/^.*(http:\/\/.*)$/', $url, $matches))
                {
                    $url = $matches[1];
                }
                $source = $pq->find('div.slp >span.news-source')->html();
                $date   = $pq->find('div.slp >span.nsa')->html();
                $desc   = $pq->find('div.st')->html();


                $res[] = array(
                    'query_phrase'  => $query,
                    'source_domain' => $source,
                    'url'           => $url,
                    'title'         => $title,
                    'description'   => $desc,
                    'date'          => gmdate('Y-m-d'));
                
            }
        }
        return $res;
    }

    public function getRelated($url)
    {
        $res = array();
        $url = "https://www.youtube.com/watch?v=". urlencode($url);
        $html = $this->getPage($url);
        phpQuery::newDocument($html);
        // all LIs from last selected DOM
        foreach(pq('#watch-related')->find('li.video-list-item > a.related-video') as $item)
        {
            $pq = pq($item);
            
            $title = $pq->find('span.title')->text();
            $url   = $pq->attr('href');
            if (preg_match('/^.*(http:\/\/.*)$/', $url, $matches))
            {
                $url = $matches[1];
            }
            
            // 
            if (preg_match('/^\/watch\?v=(.*)$/', $url, $matches))
            {
                $url = $matches[1];
            }
            
            //$source = $pq->find('p.yt-lockup2-meta > a')->html();
            //$date   = $pq->find('div.slp >span.nsa')->html();
            //$desc   = $pq->find('p.yt-lockup2-description')->html();
            $thumb    = $pq->find('img[data-thumb]')->attr('data-thumb');
            
            
            $res[] = array(
                'url'           => $url,
                'title'         => $title,
                'thumb'         => $thumb);
            
        }
        return $res;        
    }

    public function image($query)
    {
        $res = array();
        $url = "https://www.google.com/search?num=10&biw=1278&bih=900&site=imghp&tbm=isch&source=hp&biw=1278&bih=900&q=". urlencode($query);

        $number = 0;
        $page = 0;
        while($page < 1)/** @todo there are not pages in images search on google */
        {
            $html = $this->getPage($url);
            $page += 10;
            phpQuery::newDocument($html);
            // all LIs from last selected DOM
            foreach(pq('div#rg_s')->find('div.rg_di') as $item)
            {
                $number++;
                if($number > $this->number)
                {
                    break 2;
                }
                
                $pq = pq($item);
                
                //$title = $pq->find('h3.r > a')->html();
                $url   = $pq->find('a.rg_l')->attr('href');
                if (preg_match('/^.*(http:\/\/.*)$/', $url, $matches))
                {
                    $url = $matches[1];
                }
                //$source = $pq->find('div.slp >span.news-source')->html();
                //$date   = $pq->find('div.slp >span.nsa')->html();
                //$desc   = $pq->find('div.st')->html();


                $res[] = array(
                    'query_phrase'  => $query,
                    'url'           => $url,
                    'img'           => substr($url, 0, strpos($url, '&')),
                    'date'          => gmdate('Y-m-d'));
                
            }
        }
        return $res;
    }

    public function get($query)
    {
        $res = array();
        foreach($this->sources as $source)
        {
            $res = array_merge($res, $this->search($query, $source['domain']));
        }
        
        return array('search'  => $res,
                     'news'    => $this->news($query),
                     'youtube' => $this->youtube($query),
                     'image'   => $this->image($query));
    }
    

}